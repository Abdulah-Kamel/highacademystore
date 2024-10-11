<?php

namespace App\Http\Controllers\User;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\product as objModel;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;


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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $product_qty = $request->input('product_qty');
        $product_id = $request->input('product_id');
        $product = Product::getProductByCart($product_id);


        if (!$product || $product[0]['state'] == 0) {
            $response['status'] = false;
            $response['message'] = "العنصر غير متاح حاليًا";
            return json_encode($response);
        }

        $price = $product[0]['price'];

        // return $price;
        $cart_array = [];
        foreach (Cart::instance('shopping')->content() as $item) {
            $cart_array[] = $item->id;
        }
        $result = Cart::instance('shopping')->add($product_id, $product[0]['name'], $product_qty, $price)->associate('App\Models\Product');

        if ($result) {
            $response['status'] = true;
            $response['product_id'] = $product_id;
            $response['total'] = Cart::subtotal();
            $response['cart_count'] = Cart::instance('shopping')->count();
            $response['message'] = "Item was added to your cart";
        }
        if ($request->ajax()) {
            $header = view('user.layouts.nav')->render();
            // $minicart=view('web.layout.inc._minicart')->render();
            $response['header'] = $header;
            // $response['minicart']=$minicart;
        }


        return json_encode($response);
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
    public function update(Request $request)
    {
        $this->validate($request, [
            'product_qty' => 'required|numeric',
        ]);
        $rowId = $request->input('rowId');
        $request_quantity = $request->input('product_qty');
        if ($request_quantity < 1) {
            $message = "you can't add less than 1 quantity";
            $response['status'] = false;
        } else {
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
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->input('cart_id');
        $result = Cart::instance('shopping')->remove($id);

        // if($result) {
        $response['status'] = true;
        $response['total'] = Cart::subtotal();
        $response['cart_count'] = Cart::instance('shopping')->count();
        $response['message'] = "Item successfully removed";
        // }
        if ($request->ajax()) {
            $header = view('user.layouts.nav')->render();
            $response['header'] = $header;
        }
        return json_encode($response);
    }
}
