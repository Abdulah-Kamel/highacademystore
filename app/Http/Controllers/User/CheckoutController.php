<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Nafezly\Payments\Classes\TapPayment;
use Gloudemans\Shoppingcart\Facades\Cart;
use Nafezly\Payments\Classes\FawryPayment;


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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    private function uploadImage($image, $folder)
    {
        $image_name = time() . "_" . $image->getClientOriginalName();
        $image->storeAs("public/" . $folder, $image_name);

        return $image_name;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->date = now();
        $order->status = "new";
        $order->cash_number = $request->cash_number;
        $order->instapay = $request->instapay;
        $order->code = Str::upper("#" . Str::random(8));

        if ($request->hasFile("image")) {
            $image_name = $this->uploadImage(
                $request->file("image"),
                "images/screens"
            );
            $order->image = $image_name;
        }

        $order->save();

        $products = $request->product_id;

        $totalOfferDiscount = 0;
        $totalNetTotalPrice = 0;

        foreach ($products as $key => $product) {
            $orderDetailsData = [
                "order_id" => $order->id,
                "product_id" => $product,
                "amout" => $request->amount[$key],
                "price" => str_replace(",", "", $request->price[$key]),
                "total_price" => str_replace(",", "", $request->total_price[$key]),
            ];
            $totalNetTotalPrice += $orderDetailsData["total_price"];
            OrderDetail::create($orderDetailsData);
        }

        $order->total = $request->all_total ?? 0;

        $order->save();

        Cart::instance("shopping")->destroy();
        return redirect()->route("user.home");
    }
    public function manual_pay(Request $request)
    {
        if ($request->method !== "instapay" && $request->method !== "E-Wallets") {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "وسيلة الدفع غير مقبولة",
                ],
                400
            );
        }

        if (empty($request->account)) {
            if ($request->method == "instapay") {
                $msg = "برجاء ادخال يوزر انستا باي الذي استخدمته فالتحويل";
            } elseif ($request->method == "E-Wallets") {
                $msg = "برجاء ادخال رقم المحفظة الذي استخدمته فالتحويل";
            }
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => $msg,
                ],
                400
            );
        }

        if (Cart::instance("shopping")->count() == 0) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "سلة المشتريات فارغة",
                ],
                400
            );
        }

        if (empty($request->address)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "العنوان التفصيلي مطلوب",
                ],
                400
            );
        }

        if (!$request->hasFile("screenshot")) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "ايصال التحويل مطلوب",
                ],
                400
            );
        }
        
        
        if (empty($request->user_name)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "الاسم ثلاثي مطلوب",
                ],
                400
            );
        }
        
         if (empty($request->mobile)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "رقم الموبايل مطلوب",
                ],
                400
            );
        }

        $governoratesData = json_decode(
            File::get(storage_path("cities/governorates.json")),
            true
        );
        $citiesData = json_decode(
            File::get(storage_path("cities/cities.json")),
            true
        );

        $gov_name =
            $governoratesData[$request->government - 1]["governorate_name_ar"];
        $city_name = $citiesData[$request->city - 1]["city_name_ar"];
        $address = $gov_name . " - " . $city_name;
        $gov_delivery = $governoratesData[$request->government - 1]["price"];

        try {
            DB::beginTransaction();

            $amount = array_sum(
                array_map(function ($value) {
                    return str_replace(",", "", $value);
                }, $request->total_price)
            );

            if (isset($request->fast_ship) && $request->fast_ship == "on") {
                $delivery_fee =
                    $gov_delivery + Cart::instance("shopping")->count() * 15 + 30;
            } else {
                $delivery_fee =
                    $gov_delivery + Cart::instance("shopping")->count() * 15;
            }

            $total = $amount + $delivery_fee;

            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->date = now();
            $order->status = "new";
            $order->method = $request->method;
            $order->account = $request->account;
            $order->code = Str::upper("#" . Str::random(8));
            $order->address = $address;
            $order->address2 = $request->address;
            $order->amount = $amount;
            $order->delivery_fee = $delivery_fee;
            $order->name= $request->user_name;
            $order->mobile = $request->mobile;
            $order->total = $total;

            if (isset($request->fast_ship) && $request->fast_ship == "on") {
                $order->is_fastDelivery = "1";
            }

            if ($request->hasFile("screenshot")) {
                $file = $request->file("screenshot");
                $filename = date("YmdHi") . $file->getClientOriginalName();
                $file->move(public_path("images/reciept"), $filename);
                $order->image = $filename;
            }

            $order->save(); // حفظ الطلب هنا للحصول على معرف الطلب

            $products = $request->product_id;
            $totalOfferDiscount = 0;
            $totalNetTotalPrice = 0;

            foreach ($products as $key => $product) {
                $orderDetailsData = [
                    "order_id" => $order->id, // استخدام معرف الطلب هنا
                    "product_id" => $product,
                    "amout" => $request->amount[$key],
                    "price" => str_replace(",", "", $request->price[$key]),
                    "total_price" => str_replace(",", "", $request->total_price[$key]),
                ];
                $totalNetTotalPrice += $orderDetailsData["total_price"];
                OrderDetail::create($orderDetailsData);
            }

            Cart::instance("shopping")->destroy();
            DB::commit();
            return response()->json(
                [
                    "success" => true,
                    "code" => 200,
                    "msg" => "تم ارسال الاوردر للمراجعة ",
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "خطأ اثناء تاكيد الاوردر",
                    "info" => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function cards_pay(Request $request)
    {
        if (Cart::instance("shopping")->count() == 0) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "سلة المشتريات فارغة",
                ],
                400
            );
        }

        if (empty($request->address)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "العنوان التفصيلي مطلوب",
                ],
                400
            );
        }
        
        
        if (empty($request->user_name)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "الاسم ثلاثي مطلوب",
                ],
                400
            );
        }
        
         if (empty($request->mobile)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "رقم الموبايل مطلوب",
                ],
                400
            );
        }

        $governoratesData = json_decode(
            File::get(storage_path("cities/governorates.json")),
            true
        );
        $citiesData = json_decode(
            File::get(storage_path("cities/cities.json")),
            true
        );

        $gov_name =
            $governoratesData[$request->government - 1]["governorate_name_ar"];
        $city_name = $citiesData[$request->city - 1]["city_name_ar"];
        $address = $gov_name . " - " . $city_name;
        $gov_delivery = $governoratesData[$request->government - 1]["price"];

        try {
            DB::beginTransaction();

            $amount = array_sum(
                array_map(function ($value) {
                    return str_replace(",", "", $value);
                }, $request->total_price)
            );

            if (isset($request->fast_ship) && $request->fast_ship == "on") {
                $delivery_fee =
                    $gov_delivery + Cart::instance("shopping")->count() * 10 + 30;
            } else {
                $delivery_fee =
                    $gov_delivery + Cart::instance("shopping")->count() * 10;
            }

            $total = $amount + $delivery_fee;

            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->date = now();
            $order->status = "new";
            $order->method = "Credit Card";
            $order->code = Str::upper("#" . Str::random(8));
            $order->address = $address;
            $order->address2 = $request->address;
            $order->amount = $amount;
            $order->delivery_fee = $delivery_fee;
            $order->name= $request->user_name;
            $order->mobile = $request->mobile;
            $order->total = $total;

            if (isset($request->fast_ship) && $request->fast_ship == "on") {
                $order->is_fastDelivery = "1";
            }

            $order->save(); // حفظ الطلب هنا للحصول على معرف الطلب

            $products = $request->product_id;
            $totalOfferDiscount = 0;
            $totalNetTotalPrice = 0;

            foreach ($products as $key => $product) {
                $orderDetailsData = [
                    "order_id" => $order->id, // استخدام معرف الطلب هنا
                    "product_id" => $product,
                    "amout" => $request->amount[$key],
                    "price" => str_replace(",", "", $request->price[$key]),
                    "total_price" => str_replace(",", "", $request->total_price[$key]),
                ];
                $totalNetTotalPrice += $orderDetailsData["total_price"];
                OrderDetail::create($orderDetailsData);
            }

            $amount_to_pay = $order->total + $order->total * 0.04;
            $payment = new TapPayment();
            $response = $payment->pay(
                $amount_to_pay,
                $user_id = 1,
                $user_first_name = $order->users->name,
                $user_last_name = $order->users->name,
                $user_email = $order->users->email,
                $user_phone = $order->users->phone,
                $source = "src_cards"
            );

            $order->payment_id = $response["payment_id"];
            $order->account = $response["payment_id"];
            $order->save();

            Cart::instance("shopping")->destroy();
            DB::commit();
            return response()->json(
                [
                    "success" => true,
                    "code" => 200,
                    "url" => $response["redirect_url"],
                ],
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "خطأ اثناء الدفع",
                    "info" => $e->getMessage(),
                ],
                400
            );
        }
    }

    public function fawry_pay(Request $request)
    {
        if (Cart::instance("shopping")->count() == 0) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "سلة المشتريات فارغة",
                ],
                400
            );
        }

        if (empty($request->address)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "العنوان التفصيلي مطلوب",
                ],
                400
            );
        }

        if (empty($request->user_name)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "الاسم ثلاثي مطلوب",
                ],
                400
            );
        }
        
         if (empty($request->mobile)) {
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "رقم الموبايل مطلوب",
                ],
                400
            );
        }

        $governoratesData = json_decode(
            File::get(storage_path("cities/governorates.json")),
            true
        );
        $citiesData = json_decode(
            File::get(storage_path("cities/cities.json")),
            true
        );

        $gov_name =
            $governoratesData[$request->government - 1]["governorate_name_ar"];
        $city_name = $citiesData[$request->city - 1]["city_name_ar"];
        $address = $gov_name . " - " . $city_name;
        $gov_delivery = $governoratesData[$request->government - 1]["price"];

        try {
            DB::beginTransaction();

            $amount = array_sum(
                array_map(function ($value) {
                    return str_replace(",", "", $value);
                }, $request->total_price)
            );

            if (isset($request->fast_ship) && $request->fast_ship == "on") {
                $delivery_fee =
                    $gov_delivery + Cart::instance("shopping")->count() * 10 + 30;
            } else {
                $delivery_fee =
                    $gov_delivery + Cart::instance("shopping")->count() * 10;
            }

            $total = $amount + $delivery_fee;

            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->date = now();
            $order->status = "new";
            $order->method = "Fawry Pay";
            $order->code = Str::upper("#" . Str::random(8));
            $order->address = $address;
            $order->address2 = $request->address;
            $order->amount = $amount;
            $order->delivery_fee = $delivery_fee;
            $order->name= $request->user_name;
            $order->mobile = $request->mobile;
            $order->total = $total;

            if (isset($request->fast_ship) && $request->fast_ship == "on") {
                $order->is_fastDelivery = "1";
            }

            $order->save();

            $products = $request->product_id;
            $totalOfferDiscount = 0;
            $totalNetTotalPrice = 0;

            foreach ($products as $key => $product) {
                $orderDetailsData = [
                    "order_id" => $order->id,
                    "product_id" => $product,
                    "amout" => $request->amount[$key],
                    "price" => str_replace(",", "", $request->price[$key]),
                    "total_price" => str_replace(",", "", $request->total_price[$key]),
                ];
                $totalNetTotalPrice += $orderDetailsData["total_price"];
                OrderDetail::create($orderDetailsData);
            }

            $amount_to_pay = $order->total + ($order->total * 0.01) + 2.5;

            /*
                  $payment = new TapPayment();
                  $response = $payment->pay(
                      $amount_to_pay,
                      $user_id = 1,
                      $user_first_name = $order->users->name,
                      $user_last_name = $order->users->name,
                      $user_email = $order->users->email,
                      $user_phone = $order->users->phone,
                      $source = "src_eg.fawry"
                  );
      */
            $currentDateTime = Carbon::now();
            $futureDateTime = $currentDateTime->addHours(4);
            $futureTimestamp = $futureDateTime->timestamp * 1000;


            $response = $this->createLink(
                $amount = $amount_to_pay,
                $user_id = $order->users->id,
                $user_name = $order->users->name,
                $email = $order->users->email,
                $phone = $order->users->phone,
                $method = "PayAtFawry",
                $ref = $order->id,
                $exp = $futureTimestamp,
                $redirect_url = route("verify-payment")
            );

            if (isset($response["code"]) && $response["code"] == "200") {

                $order->payment_id = $response["payment_id"];
                $order->account = $response["payment_id"];
                $order->save();

                Cart::instance("shopping")->destroy();
                DB::commit();
                return response()->json(
                    [
                        "success" => true,
                        "code" => 200,
                        "url" => $response["link"],
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        "success" => false,
                        "code" => 400,
                        "msg" => "خطأ اثناء الدفع",
                    ],
                    400
                );
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    "success" => false,
                    "code" => 400,
                    "msg" => "خطأ اثناء الدفع",
                    "info" => $e->getMessage(),
                ],
                400
            );
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
        $redirect_url = null
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
            "orderWebHookUrl" => route('fawry.webhook'),
            "authCaptureModePayment" => false,
            "signature" => "$signature",
        ];

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
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
