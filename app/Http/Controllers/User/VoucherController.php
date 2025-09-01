<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Voucher;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Nafezly\Payments\Classes\FawryPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\VouchersOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VoucherController extends Controller
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


  public function index(Request $request)
{
    
    $vouchers = Voucher::where('coupon_id', $request->id)->where('is_used', 0)->count();
    $validator = Validator::make($request->all(), [
        'qty' => "required|integer|min:1|max:$vouchers", 
        'id' => 'required|exists:coupons,id',
    ], [
        'qty.required' => 'حقل الكمية مطلوب.',
        'qty.integer' => 'يجب أن تكون الكمية رقمًا صحيحًا.',
        'qty.min' => 'يجب أن تكون الكمية على الأقل 1.',
        'qty.max' => "لا يمكن أن تتجاوز الكمية $vouchers.",
        'id.required' => 'معرّف القسيمة مطلوب.',
        'id.exists' => 'القسيمة المحددة غير موجودة.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    $coupon = Coupon::find($request->id);
    return view('user.vouchers_checkout', compact('request', 'coupon'));

}

  
 public function payment(Request $request)
{
    $vouchers = Voucher::where('coupon_id', $request->id)->count();

    $validator = Validator::make($request->all(), [
        'qty' => "required|integer|min:1|max:$vouchers",
        'id' => 'required|exists:coupons,id',
        'type' => 'required|in:fawry,wallets'
    ],[
        'qty.required' => 'حقل الكمية مطلوب.',
        'qty.integer' => 'يجب أن تكون الكمية رقمًا صحيحًا.',
        'qty.min' => 'يجب أن تكون الكمية على الأقل 1.',
        'qty.max' => "لا يمكن أن تتجاوز الكمية $vouchers.",
        'id.required' => 'معرّف القسيمة مطلوب.',
        'id.exists' => 'القسيمة المحددة غير موجودة.',
        'type.required' => "وسيلة الدفع غير معروفة",
        'type.in' => "وسيلة الدفع غير معروفة"
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422); // إرجاع الأخطاء
    }

    try {
        DB::beginTransaction();

        $coupon = Coupon::findOrFail($request->id);
        $amount_to_pay = $coupon->price * $request->qty;
        $fee = $amount_to_pay * 0.01 + 2.5;
        $total = $amount_to_pay + $fee;
        $user = Auth::user();

        $order = VouchersOrder::create([
            "coupon_id" => $coupon->id,
            "user_id" => $user->id,
            "user_name" => $user->name,
            "user_email" => $user->email,
            "user_phone" => $user->phone,
            "quantity" => $request->qty,
        ]);

        $currentDateTime = Carbon::now();
        $futureDateTime = $currentDateTime->addHours(4);
        $futureTimestamp = $futureDateTime->timestamp * 1000;
       
        if($request->type == "fawry"){
            $type = "PayAtFawry";
        }elseif($request->type == "wallets"){
            $type = "MWALLET";
        }

        $response = $this->createLink(
            $amount = $total,
            $user_id = $user->id,
            $user_name = $user->name,
            $email = $user->email,
            $phone = $user->phone,
            $method = $type,
            $ref = rand(100 , 9999)."-".$order->id,
            $exp = $futureTimestamp,
            $redirect_url = route("voucher.checkout")
        );

        if (isset($response["code"]) && $response["code"] == "200") {
            $order->ref_code = $response["payment_id"];
            $order->method = "FawryPay";
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء عملية الدفع بنجاح.',
                'redirect_url' => $response["link"],
            ]);
        }

        DB::rollBack();
        return response()->json(['message' => 'خطأ أثناء إنشاء عملية الدفع.'], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب.', 'error' => $e->getMessage()], 500);
    }
}
public function manual_payment(Request $request)
{
    $vouchers = Voucher::where('coupon_id', $request->id)->count();

    $validator = Validator::make($request->all(), [
        'qty' => "required|integer|min:1|max:$vouchers",
        'id' => 'required|exists:coupons,id',
        'type' => 'required',
        "screenshot" => "required|image",
        "account" => "required|regex:/^01[0-9]{9}$/"

    ],[
        'qty.required' => 'حقل الكمية مطلوب.',
        'qty.integer' => 'يجب أن تكون الكمية رقمًا صحيحًا.',
        'qty.min' => 'يجب أن تكون الكمية على الأقل 1.',
        'qty.max' => "لا يمكن أن تتجاوز الكمية $vouchers.",
        'id.required' => 'معرّف القسيمة مطلوب.',
        'id.exists' => 'القسيمة المحددة غير موجودة.',
        'type.required' => "وسيلة الدفع غير معروفة",
        "screenshot.required" => "حقل الصورة مطلوب",
        "screenshot.image" => "يجب أن تكون الصورة ملف صورة",
        "account.required" => "حقل الرقم المحول منه مطلوب"
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422); // إرجاع الأخطاء
    }

    try {
        DB::beginTransaction();

        $coupon = Coupon::findOrFail($request->id);
        $amount_to_pay = $coupon->price * $request->qty;
        $fee = $amount_to_pay * 0.01 + 2.5;
        $total = $amount_to_pay + $fee + (($amount_to_pay + $fee) * 0.015);
        $user = Auth::user();

        $image = "";

        if ($request->hasFile("screenshot")) {
            $file = $request->file("screenshot");
            $filename = date("YmdHi") . $file->getClientOriginalName();
            $file->move(public_path("images/reciept"), $filename);
            $image = $filename;
        }

        $order = VouchersOrder::create([
            "coupon_id" => $coupon->id,
            "user_id" => $user->id,
                "user_name" => $user->name,
                "user_email" => $user->email,
                "user_phone" => $user->phone,
            "quantity" => $request->qty,
            'image' => $image,
            "method" => "manual",
            'ref_code' => Str::random(10),
            'state' => 'pending',
            "account" => $request->account
        ]);

        $order->save();
        DB::commit();

        return response()->json([
            'code' => 200,
            'message' => 'تم إنشاء عملية الدفع بنجاح.'
        ]);

        DB::rollBack();
        return response()->json(['message' => 'خطأ أثناء إنشاء عملية الدفع.'], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'حدث خطأ أثناء معالجة الطلب.', 'error' => $e->getMessage()], 500);
    }
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

        $stringToHash = $this->fawry_merchant . $ref . $user_id . $redirect_url . $randomCode . "1" . number_format($amount, 2, '.', '') . $this->fawry_secret;

        // حساب التوقيع باستخدام SHA-256
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
            "orderWebHookUrl" => route('vouchers.webhook'),
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
    
     public function callBack(Request $request)
    {
         $state = "coupon-pend";
        $ref = $request->referenceNumber;
        $compact = compact('state', 'ref');
        return view('payment_result', $compact);
           
    }

  


    public function fawry_webhook(Request $request)
    {


        $data = $request->all();

        $jsonText = json_encode($data, JSON_PRETTY_PRINT);
        $timestamp = now()->format('Y-m-d_H-i-s'); 
        $fileName = "logs/webhook_request_{$timestamp}.json";
        Storage::put($fileName, $jsonText);


        if (isset($request->orderStatus)) {

            $fawryRefNumber = $data['fawryRefNumber'];
            $merchantRefNumber = $data['merchantRefNumber'];
            $paymentAmount = number_format($data['paymentAmount'], 2, '.', '');
            $orderAmount = number_format($data['orderAmount'], 2, '.', '');
            $orderStatus = $data['orderStatus'];
            $paymentMethod = $data['paymentMethod'];
            $paymentReferenceNumber = isset($data['paymentRefrenceNumber']) ? $data['paymentRefrenceNumber'] : '';
            $secureKey = config("nafezly-payments.FAWRY_SECRET");

            $concatenatedString = $fawryRefNumber . $merchantRefNumber . $paymentAmount . $orderAmount . $orderStatus . $paymentMethod . $paymentReferenceNumber . $secureKey;
            $sha256Digest = hash('sha256', $concatenatedString);
            
            
       
            if ($request->messageSignature !== $sha256Digest) {
                return "error signature";
            }
        } else {
            return "unknown status";
        }
        
        $order_code = explode("-",$request->merchantRefNumber);
        
        $order = VouchersOrder::where("id", $order_code[1])->where("user_id", $request->customerMerchantId)->where("state", "pending")->first();
        if (!$order) {
            
            return "not found";
        }

     
        if ($request->orderStatus == "PAID") {
        $order->update([
        "state" => "success"
        ]);
        
          $coupon = Coupon::findOrFail($order->coupon_id);
          $qty = $order->quantity;

          $vouchers = Voucher::where("coupon_id", $coupon->id)
           ->where("is_used", 0)
           ->take($qty)
           ->get();


           if ($vouchers->count() < $qty) {
             return response()->json(["error" => "Not enough vouchers available"], 400);
           }

  
           foreach ($vouchers as $voucher) {
           $voucher = Voucher::find($voucher->id);
           $voucher->is_used = 1;
           $voucher->user_id=  $request->customerMerchantId;
           $voucher->save();
           }
        }elseif ($request->orderStatus == "UNPAID") {
            $order->status = "cancelled";
            $order->save();
        }
        return $request->orderStatus;
    }

}
