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

    /**
     * كتابة لوج إلى الملف
     */
    private function logToFile($filePath, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($filePath, $logMessage, FILE_APPEND | LOCK_EX);
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
        
        // إنشاء معرف فريد للعملية
        $processId = 'fawry_' . time() . '_' . uniqid();
        $logFile = storage_path("logs/fawry_webhooks/{$processId}.log");
        
        // إنشاء المجلد إذا لم يكن موجود
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        // بداية التسجيل
        $this->logToFile($logFile, "=== FAWRY WEBHOOK PROCESS START ===");
        $this->logToFile($logFile, "Process ID: {$processId}");
        $this->logToFile($logFile, "Timestamp: " . date('Y-m-d H:i:s'));
        $this->logToFile($logFile, "Request IP: " . $request->ip());
        $this->logToFile($logFile, "Request Method: " . $request->method());

        // تحويل المصفوفة إلى JSON
        $jsonText = json_encode($data, JSON_PRETTY_PRINT);
        $this->logToFile($logFile, "Received data: " . $jsonText);

        // التحقق من وجود orderStatus
        $this->logToFile($logFile, "STEP 1: Checking if orderStatus exists");
        
        if (!isset($request->orderStatus)) {
            $this->logToFile($logFile, "ERROR: orderStatus not found in request");
            $this->logToFile($logFile, "=== PROCESS TERMINATED - NO ORDER STATUS ===");
            return;
        }
        
        $this->logToFile($logFile, "SUCCESS: orderStatus found - " . $request->orderStatus);
        
        // استخراج البيانات المطلوبة
        $this->logToFile($logFile, "STEP 2: Extracting required data");

        $fawryRefNumber = $data['fawryRefNumber'];
        $merchantRefNumber = $data['merchantRefNumber'];
        $paymentAmount = number_format($data['paymentAmount'], 2, '.', '');
        $orderAmount = number_format($data['orderAmount'], 2, '.', '');
        $orderStatus = $data['orderStatus'];
        $paymentMethod = $data['paymentMethod'];
        $paymentReferenceNumber = isset($data['paymentRefrenceNumber']) ? $data['paymentRefrenceNumber'] : '';
        $secureKey = config("nafezly-payments.FAWRY_SECRET");

        $this->logToFile($logFile, "Extracted data:");
        $this->logToFile($logFile, "- fawryRefNumber: {$fawryRefNumber}");
        $this->logToFile($logFile, "- merchantRefNumber: {$merchantRefNumber}");
        $this->logToFile($logFile, "- paymentAmount: {$paymentAmount}");
        $this->logToFile($logFile, "- orderAmount: {$orderAmount}");
        $this->logToFile($logFile, "- orderStatus: {$orderStatus}");
        $this->logToFile($logFile, "- paymentMethod: {$paymentMethod}");
        $this->logToFile($logFile, "- paymentReferenceNumber: {$paymentReferenceNumber}");

        // التحقق من صحة التوقيع
        $this->logToFile($logFile, "STEP 3: Verifying signature");

        $concatenatedString = $fawryRefNumber . $merchantRefNumber . $paymentAmount . $orderAmount . $orderStatus . $paymentMethod . $paymentReferenceNumber . $secureKey;
        $sha256Digest = hash('sha256', $concatenatedString);

        $this->logToFile($logFile, "Concatenated string for signature: {$concatenatedString}");
        $this->logToFile($logFile, "Generated SHA256: {$sha256Digest}");
        $this->logToFile($logFile, "Received signature: " . $request->messageSignature);

        if ($request->messageSignature !== $sha256Digest) {
            $this->logToFile($logFile, "ERROR: Signature verification failed!");
            $this->logToFile($logFile, "=== PROCESS TERMINATED - INVALID SIGNATURE ===");
            return;
        }
        
        $this->logToFile($logFile, "SUCCESS: Signature verified");

        // البحث عن الطلب
        $this->logToFile($logFile, "STEP 4: Finding order");
        $this->logToFile($logFile, "Searching for order with ID: {$merchantRefNumber} and user_id: " . $request->customerMerchantId);

        $order = Order::where("id", $request->merchantRefNumber)->where("user_id", $request->customerMerchantId)->first();
        
        if (!$order) {
            $this->logToFile($logFile, "ERROR: Order not found!");
            $this->logToFile($logFile, "=== PROCESS TERMINATED - ORDER NOT FOUND ===");
            return;
        }
        
        $this->logToFile($logFile, "SUCCESS: Order found - ID: {$order->id}");
        $this->logToFile($logFile, "Order current status: {$order->status}");
        $this->logToFile($logFile, "Order current is_paid: {$order->is_paid}");
        $this->logToFile($logFile, "Order current tracker: {$order->tracker}");

        // معالجة حالة الطلب
        $this->logToFile($logFile, "STEP 5: Processing order status - {$orderStatus}");

        if ($request->orderStatus == "PAID") {
            $this->logToFile($logFile, "Processing PAID status");
            
            $order->is_paid = 1;
            $this->logToFile($logFile, "Set is_paid to 1");
            
            $hasReservedProduct = false;
            $this->logToFile($logFile, "Checking order details for reserved products");
            
            foreach ($order->orderDetails as $detail) {
                $this->logToFile($logFile, "Processing order detail ID: {$detail->id}, product_id: {$detail->product_id}");
                
                if (!empty($detail->products)) {
                    $this->logToFile($logFile, "Product found - ID: {$detail->product_id}, State: {$detail->products->state}, Quantity: {$detail->products->quantity}");
                    Log::info("Webhook (Before Update) - Order ID: {$order->id}, Product ID: {$detail->product_id}, State: {$detail->products->state}, Quantity: {$detail->products->quantity}");
                    
                    if ($detail->products->state == 2) {
                        $hasReservedProduct = true;
                        $this->logToFile($logFile, "Reserved product detected");
                    }
                } else {
                    $this->logToFile($logFile, "No product found for detail ID: {$detail->id}");
                }
            }
            
            $orderStatus = $hasReservedProduct ? "reserved" : "success";
            $order->status = $orderStatus;
            $order->tracker = "shipped"; // second stage
            
            $this->logToFile($logFile, "Set order status to: {$orderStatus}");
            $this->logToFile($logFile, "Set tracker to: shipped");

            // إرسال الإيميل
            try {
                $this->logToFile($logFile, "Sending success email to: " . $order->user->email);
                Mail::to($order->user->email)->send(new successPaid($order));
                $this->logToFile($logFile, "Email sent successfully");
            } catch (\Exception $e) {
                $this->logToFile($logFile, "ERROR sending email: " . $e->getMessage());
            }

            $order->save();
            $this->logToFile($logFile, "Order saved successfully");

        } elseif ($request->orderStatus == "UNPAID") {
            $this->logToFile($logFile, "Processing UNPAID status");
            
            $order->is_paid = 2;
            $order->status = "cancelled";
            $order->response = $jsonText;

            $this->logToFile($logFile, "Set is_paid to 2");
            $this->logToFile($logFile, "Set status to cancelled");
            $this->logToFile($logFile, "Set response data");

            // Restore product quantities and handle state properly
            $this->logToFile($logFile, "Starting product quantity restoration");

            foreach ($order->orderDetails as $detail) {
                $this->logToFile($logFile, "Processing detail ID: {$detail->id}, product_id: {$detail->product_id}, amount: {$detail->amout}");

                $product = Product::find($detail->product_id);
                if (!$product) {
                    $this->logToFile($logFile, "Product not found for ID: {$detail->product_id}");
                    continue;
                }

                // Store original state before quantity restoration
                $originalState = $product->state;
                $originalQuantity = $product->quantity;
                
                $this->logToFile($logFile, "Product before update - State: {$originalState}, Quantity: {$originalQuantity}");

                // Restore quantity
                $product->increment('quantity', $detail->amout);
                $product->refresh();

                $this->logToFile($logFile, "Quantity restored by {$detail->amout}, new quantity: {$product->quantity}");

                // Smart state update logic for cancelled orders
                if ($originalState == 2) {
                    // If it was reserved, keep it reserved regardless of quantity
                    $product->state = 2;
                    $this->logToFile($logFile, "Kept state as reserved (2)");
                } elseif ($originalState == 0 && $product->quantity > 0) {
                    // If it was unavailable due to no stock, and now has stock, make it available
                    $product->state = 1;
                    $this->logToFile($logFile, "Changed state from unavailable (0) to available (1)");
                } elseif ($originalState == 1) {
                    // If it was available, keep it available if quantity > 0
                    $product->state = $product->quantity > 0 ? 1 : 0;
                    $this->logToFile($logFile, "Updated state based on quantity - new state: {$product->state}");
                }

                $this->logStateChange($product, $originalState, $product->state, "Order cancelled", [
                    'order_id' => $order->id,
                    'order_status' => $order->status
                ]);

                $product->save();
                $this->logToFile($logFile, "Product saved - Final state: {$product->state}, Final quantity: {$product->quantity}");
            }

            $order->save();
            $this->logToFile($logFile, "Order saved successfully");
        } else {
            $this->logToFile($logFile, "Unknown order status: {$request->orderStatus}");
        }

        $this->logToFile($logFile, "=== FAWRY WEBHOOK PROCESS COMPLETED SUCCESSFULLY ===");
        $this->logToFile($logFile, "Final order status: {$order->status}");
        $this->logToFile($logFile, "Final is_paid: {$order->is_paid}");
        $this->logToFile($logFile, "Process ended at: " . date('Y-m-d H:i:s'));
    }

    public function cronjob()
    {
        $fawry_orders = Order::where("status", "new")->where("method", "Fawry Pay")->where('created_at', '<', Carbon::now()->subHours(4))->get();
        $fawryWallet_orders = Order::where("status", "new")->where("method", "Fawry WALLET")->where('created_at', '<', Carbon::now()->subHours(4))->get();
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