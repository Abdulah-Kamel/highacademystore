<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Nafezly\Payments\Classes\TapPayment;
use Nafezly\Payments\Classes\FawryPayment;
use Nafezly\Payments\Classes\HyperPayPayment;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Mail\successPaid;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Log product state changes with detailed context
     */
    private function logStateChange($product, $oldState, $newState, $context, $additionalInfo = [])
    {
        $stateNames = [
            0 => 'Unavailable',
            1 => 'Available',
            2 => 'Reserved'
        ];

        $logData = [
            'product_id' => $product->id,
            'product_name' => $product->name ?? 'Unknown',
            'old_state' => $oldState,
            'new_state' => $newState,
            'old_state_name' => $stateNames[$oldState] ?? 'Unknown',
            'new_state_name' => $stateNames[$newState] ?? 'Unknown',
            'quantity' => $product->quantity,
            'context' => $context,
            'timestamp' => now()->toDateTimeString()
        ];

        // Add any additional info
        $logData = array_merge($logData, $additionalInfo);

        Log::info("PRODUCT STATE CHANGE: {$context}", $logData);
    }

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
                if (!empty($detail->products)) {
                    Log::info("Webhook (Before Update) - Order ID: {$order->id}, Product ID: {$detail->product_id}, State: {$detail->products->state}, Quantity: {$detail->products->quantity}");
                    if ($detail->products->state == 2) {
                        $hasReservedProduct = true;
                    }
                }
            }
            $order->status = $hasReservedProduct ? "reserved" : "success";
            $order->tracker = "shipped"; // second stage
            Mail::to($order->user->email)->send(new successPaid($order));
            $order->save();
        } elseif ($request->orderStatus == "UNPAID") {
            $order->is_paid = 2;
            $order->status = "cancelled";
            $order->response = $jsonText;

            // Restore product quantities and handle state properly
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                if (!$product) {
                    continue;
                }

                // Store original state before quantity restoration
                $originalState = $product->state;

                // Restore quantity
                $product->increment('quantity', $detail->amout);
                $product->refresh();

                // Smart state update logic for cancelled orders
                if ($originalState == 2) {
                    // If it was reserved, keep it reserved regardless of quantity
                    $product->state = 2;
                } elseif ($originalState == 0 && $product->quantity > 0) {
                    // If it was unavailable due to no stock, and now has stock, make it available
                    $product->state = 1;
                } elseif ($originalState == 1) {
                    // If it was available, keep it available if quantity > 0
                    $product->state = $product->quantity > 0 ? 1 : 0;
                }

                $this->logStateChange($product, $originalState, $product->state, "Order cancelled", [
                    'order_id' => $order->id,
                    'order_status' => $order->status
                ]);

                $product->save();
            }

            $order->save();
        }
    }

    public function cronjob()
    {
        $fawry_orders = Order::where("status", "new")->where("method", "Fawry Pay")->where('created_at', '<', Carbon::now()->subHours(3))->get();
        $fawryWallet_orders = Order::where("status", "new")->where("method", "Fawry WALLET")->where('created_at', '<', Carbon::now()->subHours(3))->get();
        $orders = Order::where("tracker", "delivered")->where('updated_at', '<', Carbon::now()->subHours(48))->get();
        $cart = Cart::instance('shopping')->content();
        $expired_cart_items = $cart->filter(function ($item) {
            // 'added_at' is stored in options when adding to cart
            $addedAt = optional($item->options)->added_at;
            if ($addedAt == null) {
                return false;
            }
            // return $addedAt < Carbon::now()->subHours(3)->timestamp;
            return $addedAt < Carbon::now()->subMinutes(5)->timestamp;
        });

        foreach ($expired_cart_items as $item) {
            $product = Product::find(optional($item->model)->id);
            if (!$product) {
                continue;
            }

            // Store original state before quantity restoration
            $originalState = $product->state;

            // Atomic restore
            $product->increment('quantity', $item->qty);
            $product->refresh();

            // Smart state update logic
            if ($originalState == 2) {
                // If it was reserved, keep it reserved regardless of quantity
                $product->state = 2;
            } elseif ($originalState == 0 && $product->quantity > 0) {
                // If it was unavailable due to no stock, and now has stock, make it available
                $product->state = 1;
            }
            // If state was 1 and quantity > 0, keep it as 1
            // If quantity is still 0, keep state as 0

            $this->logStateChange($product, $originalState, $product->state, "Cart item expired", [
                'cart_item_id' => $item->rowId,
                'cart_item_name' => $item->name
            ]);

            $product->save();
            Cart::instance('shopping')->remove($item->rowId);
        }

        foreach ($fawry_orders as $o) {
            $order = Order::find($o->id);
            if (!$order) {
                continue;
            }
            $order->status = "cancelled";
            $order->save();
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                if (!$product) {
                    continue;
                }

                // Store original state before quantity restoration
                $originalState = $product->state;

                $product->increment('quantity', $detail->amout);
                $product->refresh();

                // Smart state update logic for cancelled orders
                if ($originalState == 2) {
                    // If it was reserved, keep it reserved regardless of quantity
                    $product->state = 2;
                } elseif ($originalState == 0 && $product->quantity > 0) {
                    // If it was unavailable due to no stock, and now has stock, make it available
                    $product->state = 1;
                } elseif ($originalState == 1) {
                    // If it was available, keep it available if quantity > 0
                    $product->state = $product->quantity > 0 ? 1 : 0;
                }

                $this->logStateChange($product, $originalState, $product->state, "Order cancelled", [
                    'order_id' => $order->id,
                    'order_status' => $order->status
                ]);

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
            if (!$order) {
                continue;
            }
            $order->status = "cancelled";
            $order->save();
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                if (!$product) {
                    continue;
                }

                // Store original state before quantity restoration
                $originalState = $product->state;

                $product->increment('quantity', $detail->amout);
                $product->refresh();

                // Smart state update logic for cancelled orders
                if ($originalState == 2) {
                    // If it was reserved, keep it reserved regardless of quantity
                    $product->state = 2;
                } elseif ($originalState == 0 && $product->quantity > 0) {
                    // If it was unavailable due to no stock, and now has stock, make it available
                    $product->state = 1;
                } elseif ($originalState == 1) {
                    // If it was available, keep it available if quantity > 0
                    $product->state = $product->quantity > 0 ? 1 : 0;
                }

                $this->logStateChange($product, $originalState, $product->state, "Order cancelled", [
                    'order_id' => $order->id,
                    'order_status' => $order->status
                ]);

                $product->save();
            }
        }
    }
}
