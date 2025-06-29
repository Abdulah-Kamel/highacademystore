<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Nafezly\Payments\Classes\TapPayment;
use Nafezly\Payments\Classes\FawryPayment;
use Nafezly\Payments\Classes\HyperPayPayment;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Mail\successPaid;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{

    public function payment($order)
    {
        $orderData = Order::find($order);
        /*
        $payment = new TapPayment();
        $response = $payment->pay(
            $orderData->total,
            $user_id = 1,
            $user_first_name = $orderData->users->name,
            $user_last_name = $orderData->users->name,
            $user_email = $orderData->users->email,
            $user_phone = $orderData->users->phone,
            $source = "src_cards"
        );
        */
        $payment = new FawryPayment();
        $response = $payment->setUserFirstName($orderData->users->name)
            ->setUserLastName($orderData->users->name)
            ->setUserEmail($orderData->users->email)
            ->setUserPhone($orderData->users->phone)
            ->setAmount($orderData->total)
            ->pay();

        $orderData->update([
            "payment_id" => $response['payment_id']
        ]);
        return redirect($response['redirect_url']);
    }

    public function callBack(Request $request)
    {
        $status = $request->orderStatus;
        $method = $request->paymentMethod;

        if (isset($method) && $method == "PayAtFawry") {
            return redirect()->route('genral.payment.pend');
        }

        if (isset($method) && $method == "MWALLET") {
            return redirect()->route('genral.payment.pend');
        }

        if (isset($status) && $status == "PAID") {

            return redirect()->route('genral.payment.success');
        } else {

            return redirect()->route('genral.payment.failed');
        }

        //return redirect()->route("user.order.details", $order->id);
    }

    public function paymentSuccess(Request $request)
    {
        $state = "success";
        $compact = compact('state');
        return view('payment_result', $compact);
    }

    public function paymentaFiled(Request $request)
    {
        $state = "fail";
        $compact = compact('state');
        return view('payment_result', $compact);
    }

    public function paymentPend(Request $request)
    {
        $state = "pend";
        $ref = $request->referenceNumber;
        $compact = compact('state', 'ref');
        return view('payment_result', $compact);
    }



    public function fawry_webhook(Request $request)
    {
        $data = $request->all();

        // تحويل المصفوفة إلى JSON
        $jsonText = json_encode($data, JSON_PRETTY_PRINT);



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
                return;
            }
        } else {
            return;
        }
        $order = Order::where("id", $request->merchantRefNumber)->where("user_id", $request->customerMerchantId)->first();
        if (!$order) {
            return;
        }

        if ($request->orderStatus == "PAID") {
            $order->is_paid = 1;
            $hasReservedProduct = false;
            foreach ($order->orderDetails as $detail) {
                if (!empty($detail->products) && $detail->products->state == 2) {
                    $hasReservedProduct = true;
                    break;
                }
            }
            $order->status = $hasReservedProduct ? "reserved" : "success";
            $order->tracker = "shipped"; // second stage
            $details = [
                'id' => $order->id,
                'name' => $order->name,
                'shipping' => $order->is_fastDelivery == 0 ? "شحن لاقرب مكتب بريد" : "home",
            ];
            Mail::to($order->user->email)->send(new successPaid($details));
            $order->save();
        } elseif ($request->orderStatus == "UNPAID") {
            dd($order);
            $order->is_paid = 2;
            $order->status = "cancelled";
            $order->response = $jsonText;
            $order->save();
        }
    }

    public function cronjob()
    {
        $fawry_orders = Order::where("status", "new")->where("method", "Fawry Pay")->where('created_at', '<', Carbon::now()->subHours(4))->get();
        $fawryWallet_orders = Order::where("status", "new")->where("method", "Fawry WALLET")->where('created_at', '<', Carbon::now()->subHours(4))->get();
        $orders = Order::where("tracker", "delivered")->where('updated_at', '<', Carbon::now()->subHours(48))->get();
        $cart = Cart::instance('shopping')->content();
        $expired_cart_items = $cart->filter(function ($item) {
            if ($item->added_at == null) {
                return;
            }
            return $item->added_at < Carbon::now()->subHours(3);
        });

        foreach ($expired_cart_items as $item) {
            $product = Product::find($item->model->id);
            $product->quantity = $product->quantity + $item->qty;
            // $product->state = 1;
            $product->save();

            Cart::instance('shopping')->remove($item->rowId);
        }



        foreach ($fawry_orders as $o) {
            $order = Order::find($o->id);
            $order->status = "cancelled";
            $order->save();
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                $product->quantity = $product->quantity + $detail->amout;
                if ($product->state == 0) {
                    // $product->state = 1;
                }
                $product->save();
            }
        }
        foreach ($orders as $o) {
            $order = Order::find($o->id);
            $order->tracker = "success";
            $order->save();
        }

        foreach ($fawryWallet_orders as $o) {
            $order = Order::find($o->id);
            $order->status = "cancelled";
            $order->save();
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                $product->quantity = $product->quantity + $detail->amout;
                if ($product->state == 0) {
                    // $product->state = 1;
                }
                $product->save();
            }
        }
    }
}
