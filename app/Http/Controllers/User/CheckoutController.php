<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShippingMethod;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public $fawry_url;
    public $fawry_secret;
    public $fawry_merchant;
    public $verify_route_name;
    public $fawry_display_mode;
    public $fawry_pay_mode;

    public function __construct()
    {
        $this->fawry_url = config("nafezly-payments.FAWRY_URL");
        $this->fawry_merchant = config("nafezly-payments.FAWRY_MERCHANT");
        $this->fawry_secret = config("nafezly-payments.FAWRY_SECRET");
        $this->fawry_display_mode = config("nafezly-payments.FAWRY_DISPLAY_MODE");
        $this->fawry_pay_mode = config("nafezly-payments.FAWRY_PAY_MODE");
        $this->verify_route_name = config("nafezly-payments.VERIFY_ROUTE_NAME");
    }

    /**
     * Common validator for all payment methods
     */
    protected function validateCommon(Request $request)
    {
        $rules = [
            'user_name'          => ['required', 'string', 'min:3'],
            'mobile'             => ['required', 'string', 'regex:/^01[0125][0-9]{8}$/'],
            'temp_mobile'        => ['required', 'string', 'regex:/^01[0125][0-9]{8}$/'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'government'         => ['required', 'integer'],
            'city'               => ['required', 'integer'],
            'address'            => ['required', 'string'],
            'near_post'          => ['nullable', 'string'],  // optional
        ];
        // 👉 fetch chosen method
        $method = ShippingMethod::find($request->shipping_method_id);

        if ($method && $method->type === 'post') {
            // now require it
            $rules['near_post'][] = 'required';
        }
        Validator::make($request->all(), $rules)->validate();
    }


    /**
     * Returns [addressString, delivery_fee]
     */
    protected function calcShipping(Request $request): array
    {
        $method = ShippingMethod::findOrFail($request->shipping_method);
        // always include the DB‐stored fee
        $baseFee = $method->fee;

        // 1) Branch pickup: free
        if ($method->type === 'branch') {
            $address = "{$method->name}";
            return [$address, $baseFee];
        }

        // 2) Load gov/city JSON
        $govs   = json_decode(File::get(storage_path('cities/governorates.json')), true);
        $cities = json_decode(File::get(storage_path('cities/cities.json')), true);

        $gov  = collect($govs)->firstWhere('id', $request->government);
        $city = collect($cities)->firstWhere('id', $request->city);

        if (! $gov || ! $city) {
            throw new \Exception("Invalid governorate or city.");
        }

        $address = $gov['governorate_name_ar'] . " - " . $city['city_name_ar'];
        $base    = $gov['price'];

        // نفس طريقة الحساب القديمة
        $taxFast   = Cart::instance('shopping')->content()
            ->sum(fn($p) => $p->qty * ($p->model->tax ?? 10));
        $taxNormal = Cart::instance('shopping')->content()
            ->sum(fn($p) => $p->qty * ($p->model->slowTax ?? 10));

        if ($method->type === 'home') {
            $fee = $base + $taxFast + $baseFee;
        } else { // post
            $fee = $base + $taxNormal + $baseFee;
        }

        return [$address, $fee];
    }


    /**
     * Shared order-creation logic
     */
    protected function makeOrder(Request $request, string $paymentMethod): Order
    {
        // validate common fields
        $this->validateCommon($request);

        // shipping
        [$address, $delivery] = $this->calcShipping($request);

        // cart totals
        $amount = floatval(str_replace(',', '', Cart::instance('shopping')->total()));
        $total  = $amount + $delivery;
        $method = ShippingMethod::findOrFail($request->shipping_method);

        // discount
        $discountAmt = session('applied_discount.amount', 0);
        if ($discountAmt > 0) {
            $total = max($total - $discountAmt, 0);
        }

        // build order
        $order = new Order([
            'user_id'        => auth()->id(),
            'date'           => now(),
            'status'         => 'new',
            'method'         => $paymentMethod,
            'code'           => '#' . Str::upper(Str::random(8)),
            'amount'         => $amount,
            'delivery_fee'   => $delivery,
            'total'          => $total,
            'name'           => $request->user_name,
            'mobile'         => $request->mobile,
            'temp_mobile'    => $request->temp_mobile,
            'shipping_method' => $request->shipping_method,
            'near_post'      => $request->input('near_post', null),
            'address'        => $address,
            'address2'       => $request->input('address', ''),
            'is_paid'        => 0,
            'shipping_method_id' => $method->id,
            'shipping_name'      => $method->name,
            'shipping_address'   => $method->address,
        ]);

        // optional screenshot for manual/card
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/images/screens', $filename);
            $order->image        = $filename;
            $order->instapay     = $request->input('instapay');
            $order->cash_number  = $request->input('cash_number');
        }

        $order->save();

        // details
        foreach (Cart::instance('shopping')->content() as $item) {
            OrderDetail::create([
                'order_id'    => $order->id,
                'product_id'  => $item->id,
                'amout'       => $item->qty,       // ← الآن يطابق عمود الـ DB
                'price'       => $item->price,
                'total_price' => $item->total,
                'size'        => $item->options->size  ?? null,
                'color'       => $item->options->color ?? null,
            ]);
        }

        // increment discount usage
        if ($discountAmt > 0 && ($code = session('applied_discount.code'))) {
            Discount::where('code', $code)->first()?->increment('used');
            session()->forget('applied_discount');
        }

        return $order;
    }

    /**
     * Fawry Pay endpoint
     */
    public function fawry_pay(Request $request)
    {
        // 1) نفس الـ validation من القديم:
        $validator = Validator::make($request->all(), [
            'shipping_method' => 'required|exists:shipping_methods,id',
            'government'      => 'required|numeric',
            'city'            => 'required|numeric',
            'address'         => 'required|string',
            'user_name'       => ['required', 'regex:/^[\p{Arabic}\s]+$/u', function ($attr, $val, $fail) {
                preg_match_all('/\p{Arabic}+/u', $val, $m);
                if (count($m[0]) < 3) $fail('ادخل الاسم ثلاثى كما فى البطاقه الشخصيه');
            }],
            'mobile'          => ['required', 'digits:11', function ($attr, $val, $fail) {
                if (! preg_match('/^01[0125][0-9]{8}$/', $val)) $fail('رقم الهاتف غير صحيح');
            }],
            'temp_mobile'     => ['required', 'digits:11', function ($attr, $val, $fail) {
                if (! preg_match('/^01[0125][0-9]{8}$/', $val)) $fail('رقم الهاتف الاحتياطي غير صحيح');
            }],
        ], [
            'government.required'  => 'برجاء اختيار المحافظة',
            'city.required'        => 'برجاء اختيار المدينة',
            'address.required'     => 'برجاء ادخال العنوان التفصيلي',
            'user_name.required'   => 'برجاء ادخال الاسم',
            'user_name.regex'      => 'يجب كتابة الاسم باللغة العربية',
            'mobile.required'      => 'برجاء ادخال رقم الموبايل',
            'mobile.digits'        => 'رقم الموبايل يجب أن يتكون من 11 رقمًا',
            'temp_mobile.required' => 'برجاء ادخال رقم الموبايل الاحتياطي',
            'temp_mobile.digits'   => 'رقم الموبايل الاحتياطي يجب أن يتكون من 11 رقمًا',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'msg' => $validator->errors()->first()
            ], 400);
        }

        try {
            DB::beginTransaction();

            // 2) احتساب الشحن والعنوان (كما في calcShipping)
            [$addressStr, $delivery] = $this->calcShipping($request);

            // 3) احسب المبلغ الصافي وقيمة الدفع النهائية (مع 1% + 2.5 ثابت)
            $amount = array_sum(array_map(fn($v) => str_replace(',', '', $v), $request->total_price));
            $total  = $amount + $delivery;
            $total  = max($total - session('applied_discount.amount', 0), 0);

            // 4) أنشئ الـ Order بنفس حقول القديم
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->date = now();
            $order->status = "new";
            $order->method = "Fawry Pay";
            $order->code = Str::upper("#" . Str::random(8));
            $order->address = $addressStr;
            $order->address2 = $request->address;
            $order->amount = $amount;
            $order->delivery_fee = $delivery;
            $order->name = $request->user_name;
            $order->mobile = $request->mobile;
            $order->temp_mobile = $request->temp_mobile;
            $order->total = $total;
            $order->near_post = $request->near_post ?? null;
            $order->shipping_method = $request->shipping_method;
            $order->save();


            // 5) تفاصيل الطلب
            foreach ($request->product_id as $i => $pid) {
                OrderDetail::create([
                    'order_id'    => $order->id,
                    'product_id'  => $pid,
                    'amout'       => $request->amount[$i],
                    'size'        => $request->size[$i]  ?? null,
                    'color'       => $request->color[$i] ?? null,
                    'price'       => str_replace(',', '', $request->price[$i]),
                    'total_price' => str_replace(',', '', $request->total_price[$i]),
                ]);
            }

            // 6) بناء وإرسال رابط الدفع عبر createLink (من القديم)
            $amount_to_pay = $order->total + ($order->total * 0.01) + 2.5;

            $currentDateTime = Carbon::now();
            $futureDateTime = $currentDateTime->addHours(4);
            $futureTimestamp = $futureDateTime->timestamp * 1000;
            $response = $this->createLink(
                $amount_to_pay,
                $order->users->id,
                $order->name,
                $order->users->email,
                $order->mobile,
                "PayAtFawry",       // e.g. "PayAtFawry" من config
                $order->id,
                $futureTimestamp,
                route("verify-payment")
            );

            if (isset($response['code']) && $response['code'] === '200' && isset($response['link'])) {
                $order->payment_id = $response['payment_id'];
                $order->account    = $response['payment_id'];
                $order->save();
                Cart::instance('shopping')->destroy();
                session()->forget('applied_discount');
                DB::commit();
                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'url' => $response['link']
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    "success" => false,
                    "code" => 400,
                    "msg" => "خطأ اثناء الدفع",
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'success' => false,
                'code' => 500,
                'msg' => 'خطأ اثناء الدفع',
                'info' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Manual (Instapay / E-Wallet) payment
     */
    public function manual_pay(Request $request)
    {
        // reuse same flow
        try {
            DB::beginTransaction();
            $order = $this->makeOrder($request, $request->method ?? 'manual');
            Cart::instance('shopping')->destroy();
            DB::commit();
            return response()->json([
                'success' => true,
                'msg' => 'تم إرسال الطلب للمراجعة'
            ], 200);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'خطأ أثناء تأكيد الطلب',
                'info' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Credit-Card via Tap
     */
    public function cards_pay(Request $request)
    {
        try {
            DB::beginTransaction();
            $order      = $this->makeOrder($request, 'Credit Card');
            $amountPay  = $order->total * 1.04; // +4%
            // you’d inject and call your TapPayment here...
            // $tap = new TapPayment();
            // $resp = $tap->pay( ... );
            // assume $resp['redirect_url']
            Cart::instance('shopping')->destroy();
            DB::commit();

            // demo response
            return response()->json([
                'success' => true,
                'url' => 'https://tap.example.com/checkout/' . $order->id
            ], 200);
        } catch (\Throwable $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg' => 'خطأ أثناء الدفع',
                'info' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Apply / remove a coupon
     */
    public function applyDiscount(Request $request)
    {
        $code = trim($request->coupon_code ?? '');
        if ($code === '') {
            session()->forget('applied_discount');
            return back()->with('success', 'تمت إزالة الخصم');
        }
        $discount = Discount::where('code', $code)->first();
        if (! $discount) {
            return back()->with('error', 'الكود غير صحيح');
        }
        if ($discount->usage_limit && $discount->used >= $discount->usage_limit) {
            return back()->with('error', 'تم تجاوز الحد الأقصى');
        }
        session()->put('applied_discount', [
            'code'   => $discount->code,
            'amount' => $discount->discount,
        ]);
        return back()->with('success', 'تم تطبيق الخصم');
    }
    public function createLink(
        $amount = null,
        $user_id = null,
        $user_name = null,
        $email = null,
        $phone = null,
        $method = null,
        $ref = null,
        $exp = null,
        $redirect_url = null,
        $debitMobileWalletNo = null
    ) {
        $randomCode = Str::random(10);
        $stringToHash = $this->fawry_merchant
            . $ref
            . $user_id
            . $redirect_url
            . $randomCode
            . "1"
            . number_format($amount, 2, '.', '')
            . $this->fawry_secret;

        $signature = hash("sha256", $stringToHash);
        $data = [
            "merchantCode" => $this->fawry_merchant,
            "merchantRefNum" => $ref,
            "customerMobile" => $phone,
            "customerEmail" => $email,
            "customerName" => $user_name,
            "customerProfileId" => $user_id,
            "paymentMethod" => $method,
            "paymentExpiry" => "$exp",
            "language" => "ar-eg",
            "chargeItems" => [
                [
                    "itemId" => $randomCode,
                    "price" => $amount,
                    "quantity" => 1,
                ],
            ],
            "returnUrl" => "$redirect_url",
            "orderWebHookUrl" => route('fawry.webhook'),
            "authCaptureModePayment" => false,
            "signature" => "$signature",
        ];
        if ($debitMobileWalletNo) {
            $data["debitMobileWalletNo"] = $debitMobileWalletNo;
        }

        $response = Http::post(
            $this->fawry_url . "/fawrypay-api/api/payments/init",
            $data
        );

        if (Str::startsWith($response, 'https://')) {
            return [
                "code" => "200",
                "payment_id" => $randomCode,
                "link" => $response->body()
            ];
        } else {
            return $response;
        }
    }
}
