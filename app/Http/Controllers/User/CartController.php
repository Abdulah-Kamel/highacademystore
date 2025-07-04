<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Session;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user.cart');
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
     * Store a newly created product in the cart, with a max of 30 units per product.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse (JSON)
     */
    public function store(Request $request)
    {
        $product_qty = $request->input('product_qty');
        $product_id = $request->input('product_id');
        $object_product = Product::findorfail($product_id);
        $product = Product::getProductByCart($product_id);
        if (Product::findorfail($product_id)->state != 2) {
            if (!$product || $product[0]['state'] == 0 || $product[0]['quantity'] == 0 || $product[0]['quantity'] < $product_qty) {
                return response()->json([
                    'status' => false,
                    'message' => "العنصر غير متاح حاليًا أو الكمية غير كافية"
                ]);
            }
        }

        // [ADDED] Check if user is trying to add more than 30 in a single addition
        if ($product_qty > $object_product->max_qty_for_order) {
            return response()->json([
                'status' => false,
                'message' => "عفوا، لا يمكنك إضافة أكثر من {$object_product->max_qty_for_order} لهذا المنتج.",
            ]);
        }

        // Calculate Price (Considering Offer)
        if ($product[0]['have_offer'] == 1) {
            if ($product[0]['offer_type'] == 'percentage') {
                $price = $product[0]['price'] - ($product[0]['price'] * $product[0]['offer_value']) / 100;
            } else {
                $price = $product[0]['price'] - $product[0]['offer_value'];
            }
        } else {
            $price = $product[0]['price'];
        }

        // Check if Product Already Exists in Cart
        $existingCartItem = Cart::instance('shopping')->search(function ($cartItem) use ($product_id, $request) {
            return $cartItem->id == $product_id && $cartItem->options->color == $request->color && $cartItem->options->size == $request->size;;
        })->first();

        if ($existingCartItem) {
            // Update Quantity Instead of Adding a New Row
            $newQuantity = $existingCartItem->qty + $product_qty;
            if ($newQuantity > $object_product->max_qty_for_order) {
                return response()->json([
                    'status' => false,
                    'message' => "عفوا، لا يمكنك إضافة أكثر من {$object_product->max_qty_for_order} لهذا المنتج.",
                ]);
            }

            // [ADDED] Check if total exceeds 30
            if ($newQuantity > $object_product->max_qty_for_order) {
                return response()->json([
                    'status' => false,
                    'message' => "عفوا، لا يمكنك إضافة أكثر من {$object_product->max_qty_for_order} لهذا المنتج.",
                ]);
            }

            // Ensure Stock is Available
            if ($product[0]['quantity'] < $newQuantity) {
                return response()->json([
                    'status' => false,
                    'message' => "لا توجد هذه الكمية من هذا العنصر، الكمية المتاحة هي " . $product[0]['quantity']
                ]);
            }

            Cart::instance('shopping')->update($existingCartItem->rowId, $newQuantity);
        } else {
            $max_quantity = Product::find($product_id)->max_qty_for_order;
            // Add New Item to Cart
            Cart::instance('shopping')->add(
                $product_id,
                $product[0]['name'],
                $product_qty,
                $price,
                ['maxQuantity' => $max_quantity, 'size' => $request->size, 'color' => $request->color, 'added_at' => now()->timestamp]
            )
                ->associate('App\Models\Product');
        }

        // Deduct Stock
        $product_quantity = Product::find($product_id);
        // التحقق إذا كانت الحالة تساوي 2 قبل إنقاص الكمية
        if ($product_quantity->state != 2) {
            $product_quantity->quantity -= $product_qty;
        }
        if ($product_quantity->state != 2) {
            $product_quantity->state = ($product_quantity->quantity > 0) ? 1 : 0;
        }
        $product_quantity->save();

        // Response
        $response = [
            'status' => true,
            'product_id' => $product_id,
            'total' => Cart::subtotal(),
            'cart_count' => Cart::instance('shopping')->count(),
            'message' => "تم تحديث السلة بنجاح!"
        ];

        if ($request->ajax()) {
            $response['header'] = view('user.layouts.nav')->render();
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update product quantity in the cart, with a max of 30 units per product.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request)
    {

        $this->validate($request, [
            'product_qty' => 'required|numeric',
        ]);
        $rowId = $request->input('rowId');
        $cart = Cart::instance('shopping')->get($rowId);
        $product = Product::find($cart->model->id);
        $object_product = Product::findorfail($product->id);
        $request_quantity = $request->input('product_qty');

        // [ADDED] Check if requested new quantity is more than 30
        if ($request_quantity > $object_product->max_qty_for_order) {
            $response['status'] = false;
            $response['item'] = $cart;
            $response['max_quantity'] = $object_product->max_qty_for_order;
            $response['message'] = "عفوا، لا يمكنك إضافة أكثر من {$object_product->max_qty_for_order} لهذا المنتج.";
            return $response;
        }

        if ($request_quantity < 1) {
            $message = "you can't add less than 1 quantity";
            $response['status'] = false;
        } else {
            $cart = Cart::instance('shopping')->get($rowId);
            $product = Product::find($cart->model->id);

            if ($product->quantity < ($request_quantity - $cart->qty)) {
                $response['status'] = false;
                $response['message'] = "لا توجد هذه الكمية من هذا العنصر الكمية الموجوده هي" . $product->quantity;
                return $response;
            }

            $product->quantity = $product->quantity + ($cart->qty - $request_quantity);

            if ($product->state != 2) {
                if ($product->quantity == 0) {
                    $product->state = 0;
                } else {
                    $product->state = 1;
                }
            }
            //            if ($product->quantity == 0) {
            //                $product->state = 0;
            //            } else {
            //                $product->state = 1;
            //            }
            $product->save();

            $result = Cart::instance('shopping')->update($rowId, $request_quantity);

            $message = "Quantity was updated successfully";
            $response['status'] = true;
            $response['total'] = Cart::subtotal();
            $response['cart_count'] = Cart::instance('shopping')->count();
        }

        if ($request->ajax()) {
            $header = view('user.layouts.nav')->render();
            $response['header'] = $header;
            $response['message'] = $message;
        }
        return response()->json([
            'status'         => true,
            'item_subtotal'  => number_format(Cart::instance('shopping')->get($rowId)->subtotal(), 2),
            'total'          => Cart::instance('shopping')->subtotal(),
            'cart_count'     => Cart::instance('shopping')->count(),
        ]);
    }

    /**
     * Apply a discount coupon.
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $discount = Discount::where('code', $request->coupon_code)->first();

        if (!$discount) {
            return back()->with('error', 'الكود غير صحيح أو غير موجود');
        }

        // Check usage limit, etc.
        if ($discount->usage_limit !== null && $discount->used >= $discount->usage_limit) {
            return back()->with('error', 'عذراً، تم تجاوز الحد الأقصى لاستخدام هذا الكود');
        }

        // Store the discount details in session
        session()->put('applied_discount', [
            'code' => $discount->code,
            'amount' => $discount->discount,
        ]);

        return back()->with('success', 'تم تطبيق الكود بنجاح!');
    }

    /**
     * Remove the discount coupon from session.
     */
    public function removeDiscount()
    {
        if (session()->has('applied_discount')) {
            session()->forget('applied_discount');
        }
        return back()->with('success', 'تمت إزالة الخصم بنجاح!');
    }

    /**
     * Remove the specified product from the cart.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function destroy(Request $request)
    {
        $rowId = $request->input('cart_id');
        $cart = Cart::instance('shopping')->get($rowId);
        $result = Cart::instance('shopping')->remove($rowId);

        $product = Product::find($cart->model->id);

        $product->quantity = $product->quantity + $cart->qty;
        //        $product->state = 1;
        if ($product->state != 2) {
            $product->state = 1;
        }
        $product->save();

        $response['status'] = true;
        $response['total'] = Cart::subtotal();
        $response['cart_count'] = Cart::instance('shopping')->count();
        $response['message'] = "Item successfully removed";

        if ($request->ajax()) {
            $header = view('user.layouts.nav')->render();
            $response['header'] = $header;
        }
        return response()->json([
            'status'      => true,
            'total'       => Cart::instance('shopping')->subtotal(),
            'cart_count'  => Cart::instance('shopping')->count(),
        ]);
    }
}
