<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\MinAdmin\MinAdminController;
use App\Http\Controllers\MinAdmin\MiniCouponController;
use App\Http\Controllers\MinAdmin\MiniProductController;
use App\Http\Controllers\MinAdmin\MiniVoucherController;
use App\Http\Controllers\MinAdmin\MiniCategoryController;


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



Route::get('/mini-admin/login', [MinAdminController::class, 'login'])->name('miniadmin.login')->middleware('loginUrl');
Route::post('/mini-admin/signin', [MinAdminController::class, 'signin'])->name('miniadmin.signin');


Route::get('/mini-admin/logout', [MinAdminController::class, 'adminLogout'])->name('miniadmin.logout');

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'localeCookieRedirect']
    ],
    function () {
        Route::prefix('dashboard')->middleware('auth:mini_admin')->name('dashboard.')->group(function () {

            Route::get("miniadmin", [MinAdminController::class, "index"])->name("miniadmin");
            Route::get('order/datatable', [OrderController::class, 'datatable'])->name('order.datatable');
            Route::get("order/edit-barcode/{id}", [OrderController::class, "editbarcode"])->name("order.editbarcode");
            Route::post("order/add-barcode", [OrderController::class, "addbarcode"])->name("order.addbarcode");

            Route::get('/mini/products', [MiniProductController::class, 'index'])->name('mini.product');
            Route::get('/mini/products/create', [MiniProductController::class, 'create'])->name('mini.create.product');
            Route::get('/mini/product/datatable', [MiniProductController::class, 'datatable'])->name('mini.product.datatable');
            Route::post('/mini/store/products', [MiniProductController::class, 'store'])->name('mini.store.product');
            Route::get('/mini/product/edit/{id}', [MiniProductController::class, 'edit'])->name('mini.product.edit');
            Route::post('/mini/product/update', [MiniProductController::class, 'update'])->name('mini.product.update');
            Route::post('/mini/product/destroy', [MiniProductController::class, 'destroy'])->name('mini.product.destroy');

            //coupons
            Route::get('/mini/coupons', [MiniCouponController::class, 'index'])->name('mini.coupons');
            Route::get('/mini/coupons/datatable', [MiniCouponController::class, 'datatable'])->name('mini.coupons.datatable');
            Route::get('/mini/coupons/add', [MiniCouponController::class, 'add'])->name('mini.coupons.add');
            Route::post('/mini/coupons/store', [MiniCouponController::class, 'store'])->name('mini.coupons.store');
            Route::get('/mini/coupons/edit/{coupon}', [MiniCouponController::class, 'edit'])->name('mini.coupons.edit');
            Route::post('/mini/coupons/update/{id}', [MiniCouponController::class, 'update'])->name('mini.coupons.update');
            Route::post('/mini/dashboard/coupons/destroy', [MiniCouponController::class, 'destroy'])->name('mini.coupons.destroy');

            Route::get('/mini/vouchers/{coupon}', [MiniVoucherController::class, 'index'])->name('mini.vouchers');
            Route::get('/mini/vouchers/datatable/{coupon}', [MiniVoucherController::class, 'datatable'])->name('mini.vouchers.datatable');
            Route::get('/mini/vouchers/add/{coupon}', [MiniVoucherController::class, 'add'])->name('mini.vouchers.add');
            Route::post('/mini/vouchers/store/{coupon}', [MiniVoucherController::class, 'store'])->name('mini.vouchers.store');
            Route::get('/mini/vouchers/edit/{voucher}', [MiniVoucherController::class, 'edit'])->name('mini.vouchers.edit');
            Route::post('/mini/vouchers/update/{id}', [MiniVoucherController::class, 'update'])->name('mini.vouchers.update');
            Route::post('/mini/vouchers/destroy', [MiniVoucherController::class, 'destroy'])->name('mini.vouchers.destroy');

            Route::post('/mini/category/{id}/child', [MiniCategoryController::class, 'getChildByParentID'])->name('mini.getChildByParentID');

        });
    }
);

