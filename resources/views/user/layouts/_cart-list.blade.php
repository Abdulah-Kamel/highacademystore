<div class="row ">
    <div class="col-md-12 bg-light p-5 ">
        <div class="card text-dark bg-light " style="overflow-y: scroll;">
            <div class="card-header"><strong>Cart Table</strong></div>
            <div class="card-body">

                <table class="table bg-light table-hover">
                    <thead>
                        <tr>
                            <th scope="col"><i class="fas fa-trash-alt"></i></th>
                            <th scope="col">صورة المنتج </th>
                            <th scope="col">تفاصيل المنتج </th>
                            <th scope="col">السعر </th>
                            <th scope="col">الكميه </th>
                            <th scope="col">الاجمالي </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (Cart::instance('shopping')->content() as $item)
                        <tr>
                            <th scope="row"><i class="fas fa-times cart_delete" data-id="{{$item->rowId}}"></i></th>
                            <td><img src="{{$item->model->image_path}}" style="width: 100px" class="img-thumbnail"
                                    alt=""></td>
                            <td><a href="{{ route('user.product.show', $item->id) }}"
                                    class="nav-link text-dark">{{$item->name}}</a></td>
                            <td>{{number_format($item->price,2)}} جنيه</td>
                            <td>
                                <div class="quantity">
                                    <input type="number" class="qty-text" data-id="{{$item->rowId}}"
                                        id="qty-input-{{$item->rowId}}" step="1" min="1" max="99" name="quantity"
                                        value="{{$item->qty}}" style="width:60px">
                                    <input type="hidden" data-id="{{$item->rowId}}"
                                        data-product-quantity="{{$item->model->stock}}"
                                        id="update-cart-{{$item->rowId}}">
                                </div>
                            </td>
                            <td>{{$item->subtotal()}} جنيه</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row my-5 d-flex justify-content-between">
    <div class="col-md-6 bg-light p-5">
        <div class="row mx-1">
            <div>
                <hr>
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <p><strong>Total:</strong></p>
                    <p>{{Cart::subtotal()}} جنيه</p>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-between">
                <a href="{{ route('user.card') }}" class="btn btn-primary btn-block text-light">اتمام عمليه الدفع</a>
            </div>
        </div>
    </div>
</div>
