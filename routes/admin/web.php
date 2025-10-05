<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\MainCategoriesController;
use App\Http\Controllers\Admin\MinAdminController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StageController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\VoucherOrderController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/login', [AdminController::class, 'login'])->name('login')->middleware('loginUrl');
Route::post('/signin', [AdminController::class, 'signin'])->name('admin.signin');

Route::get('/register', [AdminController::class, 'register'])->name('register')->middleware('loginUrl');
Route::post('/signup', [AdminController::class, 'signup'])->name('admin.signup');

Route::get('/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'localeCookieRedirect']
    ],
    function () {
        Route::prefix('dashboard')->middleware('auth:admin')->name('dashboard.')->group(function () {

            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
            Route::post('/change-password', [AdminController::class, 'changePass'])->name('change.password');
            Route::Post('/update/my-account', [AdminController::class, 'AccountUpdate'])->name('update.account');

            // Brand Section
            Route::get('/teachers', [BrandController::class, 'index'])->name('teachers');
            Route::get('teachers/datatable', [BrandController::class, 'datatable'])->name('brand.datatable');
            Route::get('/teachers/create', [BrandController::class, 'create'])->name('create.teachers');
            Route::post('/store/teachers', [BrandController::class, 'store'])->name('store.teachers');
            Route::get('teachers/edit/{id}', [BrandController::class, 'edit'])->name('teachers.edit');
            Route::post('teachers/update', [BrandController::class, 'update'])->name('teachers.update');
            Route::post('teachers/destroy', [BrandController::class, 'destroy'])->name('teachers.destroy');

            //education_stages
            Route::get('/education-stages', [StageController::class, 'index'])->name('education_stages');
            Route::get('/stages/create', [StageController::class, 'create'])->name('create.education_stages');
            Route::get('stages/datatable', [StageController::class, 'datatable'])->name('stage.datatable');
            Route::post('/store/stages', [StageController::class, 'store'])->name('store.stage');
            Route::get('stages/edit/{id}', [StageController::class, 'edit'])->name('stage.edit');
            Route::post('stages/update', [StageController::class, 'update'])->name('stage.update');
            Route::post('stages/destroy', [StageController::class, 'destroy'])->name('stage.destroy');

            // Slider Section
            Route::get('/sliders', [SliderController::class, 'index'])->name('slider');
            Route::get('/sliders/create', [SliderController::class, 'create'])->name('create.slider');
            Route::get('slider/datatable', [SliderController::class, 'datatable'])->name('slider.datatable');
            Route::post('/store/sliders', [SliderController::class, 'store'])->name('store.slider');
            Route::get('slider/edit/{id}', [SliderController::class, 'edit'])->name('slider.edit');
            Route::post('slider/update', [SliderController::class, 'update'])->name('slider.update');
            Route::post('slider/destroy', [SliderController::class, 'destroy'])->name('slider.destroy');

            // Category Section
            Route::get('/categories', [CategoryController::class, 'index'])->name('category');
            Route::get('/categories/create', [CategoryController::class, 'create'])->name('create.category');
            Route::get('category/datatable', [CategoryController::class, 'datatable'])->name('category.datatable');
            Route::post('/store/categories', [CategoryController::class, 'store'])->name('store.category');
            Route::get('category/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
            Route::post('category/update', [CategoryController::class, 'update'])->name('category.update');
            Route::post('category/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
            Route::post('category/{id}/child', [CategoryController::class, 'getChildByParentID'])->name('getChildByParentID');

            //main_categories
            Route::get('/main_categories', [MainCategoriesController::class, 'index'])->name('main_categories');
            Route::get('/main_categories/create', [MainCategoriesController::class, 'create'])->name('create.main_categories');
            Route::get('main_categories/datatable', [MainCategoriesController::class, 'datatable'])->name('main_categories.datatable');
            Route::post('/store/main_categories', [MainCategoriesController::class, 'store'])->name('store.main_categories');
            Route::get('main_categories/edit/{id}', [MainCategoriesController::class, 'edit'])->name('main_categories.edit');
            Route::put('main_categories/update/{id}', [MainCategoriesController::class, 'update'])->name('main_categories.update');
            Route::post('main_categories/destroy', [MainCategoriesController::class, 'destroy'])->name('main_categories.destroy');

            // Product Section
            Route::get('/products', [ProductController::class, 'index'])->name('product');
            Route::get('/products/create', [ProductController::class, 'create'])->name('create.product');
            Route::get('product/datatable', [ProductController::class, 'datatable'])->name('product.datatable');
            Route::post('/store/products', [ProductController::class, 'store'])->name('store.product');
            Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
            Route::post('product/update', [ProductController::class, 'update'])->name('product.update');
            Route::post('product/soft-delete', [ProductController::class, 'softDelete'])->name('product.soft-delete');
            Route::post('product/force-delete', [ProductController::class, 'forceDelete'])->name('product.force-delete');

            //order
            Route::get('/orders', [OrderController::class, 'index'])->name('orders');
            Route::get('orders/datatable', [OrderController::class, 'datatable'])->name('orders.datatable');
            Route::get('orders/details/{id}', [OrderController::class, 'details'])->name('orders.details');
            Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
            Route::get('orders/export/success', [OrderController::class, 'successExport'])->name('orders.export.success');
            Route::get('orders/export/branch', [OrderController::class, 'branchExport'])->name('orders.export.branch');
            Route::get('orders/export/grouped', [OrderController::class, 'groupedExport'])->name('orders.export.grouped');
            Route::get("orders/edit-barcode/{id}", [OrderController::class, "admineditbarcode"])->name("orders.editbarcode");
            Route::get("orders/barcode", [OrderController::class, "orderbarcode"])->name("orders.barcode");
            Route::get("orders/barcode/list", [OrderController::class, "barcodeOrders"])->name("orders.barcode.list");
            Route::post("orders/add-barcode", [OrderController::class, "addbarcode"])->name("orders.addbarcode");
            Route::post("order/change-state", [OrderController::class, "changestate"])->name("changestate");

            Route::get("order/status/pending/{id}/{status}", [OrderController::class, "changeStatus"])->name("changeStatus");
            //            Route::get("order/status/success/{id}", [OrderController::class, "changeSuccessStatus"])->name("changeSuccessStatus");
            Route::get("order/edit/{id}", [OrderController::class, "editOrder"])->name("editOrder");
            Route::put("order/update/{id}", [OrderController::class, "updateOrder"])->name("updateOrder");
            Route::put("order/update/book/{id}", [OrderController::class, "updateBook"])->name("updateOrderBook");

            Route::get("order/update/all/reversed/orders", [OrderController::class, "update_all_reversed_order"])->name("update_all_reversed_order");


            //coupons
            Route::get('/coupons', [CouponController::class, 'index'])->name('coupons');
            Route::get('/coupons/datatable', [CouponController::class, 'datatable'])->name('coupons.datatable');
            Route::get('/coupons/add', [CouponController::class, 'add'])->name('coupons.add');
            Route::post('/coupons/store', [CouponController::class, 'store'])->name('coupons.store');
            Route::get('/coupons/edit/{coupon}', [CouponController::class, 'edit'])->name('coupons.edit');
            Route::post('/coupons/update/{id}', [CouponController::class, 'update'])->name('coupons.update');
            Route::post('dashboard/coupons/destroy', [CouponController::class, 'destroy'])->name('coupons.destroy');

            //Vouchers
            Route::get('/vouchers/{coupon}', [VoucherController::class, 'index'])->name('vouchers');
            Route::get('/vouchers/datatable/{coupon}', [VoucherController::class, 'datatable'])->name('vouchers.datatable');
            Route::get('/vouchers/add/{coupon}', [VoucherController::class, 'add'])->name('vouchers.add');
            Route::post('/vouchers/store/{coupon}', [VoucherController::class, 'store'])->name('vouchers.store');
            Route::get('/vouchers/edit/{voucher}', [VoucherController::class, 'edit'])->name('vouchers.edit');
            Route::post('/vouchers/update/{id}', [VoucherController::class, 'update'])->name('vouchers.update');
            Route::post('/vouchers/destroy', [VoucherController::class, 'destroy'])->name('vouchers.destroy');

            // Slider Section
            Route::get('/minadmin', [MinAdminController::class, 'index'])->name('minadmin');
            Route::get('/minadmins/create', [MinAdminController::class, 'create'])->name('create.minadmin');
            Route::get('minadmin/datatable', [MinAdminController::class, 'datatable'])->name('minadmin.datatable');
            Route::post('/store/minadmins', [MinAdminController::class, 'store'])->name('store.minadmin');
            Route::get('minadmin/edit/{id}', [MinAdminController::class, 'edit'])->name('minadmin.edit');
            Route::post('minadmin/update', [MinAdminController::class, 'update'])->name('minadmin.update');
            Route::post('minadmin/destroy', [MinAdminController::class, 'destroy'])->name('minadmin.destroy');


            Route::get('/offers', [OfferController::class, 'index'])->name('offers');
            Route::get('/offers/create', [OfferController::class, 'create'])->name('create.offers');
            Route::get('/offers/datatable', [OfferController::class, 'datatable'])->name('offers.datatable');
            Route::post('/store/offers', [OfferController::class, 'store'])->name('store.offers');
            Route::get('offers/edit/{id}', [OfferController::class, 'edit'])->name('offers.edit');
            Route::put('offers/update/{id}', [OfferController::class, 'update'])->name('offers.update');
            Route::post('offers/destroy', [OfferController::class, 'destroy'])->name('offers.destroy');
            // User Route to view offers

            // Coupons (Discounts) Routes
            Route::get('/discount', [DiscountController::class, 'index'])->name('discount');
            // Returns coupon data for AJAX data table
            Route::get('/discount/datatable', [DiscountController::class, 'datatable'])->name('discount.datatable');
            // Shows the form to add a new coupon
            Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');
            // Processes the form submission to store a new coupon
            Route::post('/discount/store', [DiscountController::class, 'store'])->name('discount.store');
            // Shows the edit form for a given coupon (using route model binding for {coupon})
            Route::get('/discount/edit/{coupon}', [DiscountController::class, 'edit'])->name('discount.edit');
            // Processes the update form submission for a coupon
            Route::post('/discount/update/{id}', [DiscountController::class, 'update'])->name('discount.update');
            // Deletes a coupon
            Route::post('/discount/destroy', [DiscountController::class, 'destroy'])->name('discount.destroy');
            Route::post('/discount/toggle', [DiscountController::class, 'toggleDiscountFeature'])
                ->name('discount.toggle');


            Route::get('/voucher_order', [VoucherOrderController::class, 'index'])->name('voucher_order');
            Route::get('/voucher_order/datatable', [VoucherOrderController::class, 'datatable'])->name('voucher_order.datatable');
            Route::get('/voucher_order/details/{id}', [VoucherOrderController::class, 'details'])->name('voucher_order.details');
            Route::post("/voucher_order/change-state", [VoucherOrderController::class, "changestate"])->name("voucher_order.changestate");

            // Shipping Methods
            Route::get('/shipping-methods', [ShippingMethodController::class, 'index'])->name('shipping-methods');
            Route::get('/shipping-methods/datatable', [ShippingMethodController::class, 'datatable'])->name('shipping-methods.datatable');
            Route::get('/shipping-methods/create', [ShippingMethodController::class, 'create'])->name('shipping-methods.create');
            Route::post('/shipping-methods', [ShippingMethodController::class, 'store'])->name('shipping-methods.store');
            Route::get('/shipping-methods/{shipping_method}/edit', [ShippingMethodController::class, 'edit'])->name('shipping-methods.edit');
            Route::match(['put', 'patch'], '/shipping-methods/{shipping_method}', [ShippingMethodController::class, 'update'])->name('shipping-methods.update');
            Route::delete('/shipping-methods/{shipping_method}', [ShippingMethodController::class, 'destroy'])->name('shipping-methods.destroy');
        });
    }
);
