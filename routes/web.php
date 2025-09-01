<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\VoucherController;
use App\Http\Controllers\User\UserAddressController;
use App\Http\Controllers\ForgotPasswordController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\User\OfferController as UserOfferController;

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

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'localeCookieRedirect']
    ],
    function () {
        /**** privacy snd terms ****/
        Route::view("privacy-policy", "privacy")->name("privacy");
        Route::view("terms", "terms")->name("terms");

        /**** PASSWORD ROUTES ****/
        Route::view("/forgot-password", "user.password.sendlink")->name("password.request")->middleware("guest");
        Route::post("/forgot-password", [ForgotPasswordController::class, "sendResetLinkEmail"])->middleware("guest")->name("password.email");
        Route::get("/reset-password/{token}", [ForgotPasswordController::class, "newpassform"])->middleware("guest")->name("password.reset");
        Route::post("/reset-password", [ForgotPasswordController::class, "reset"])->name("password.update")->middleware("guest");
        Route::put('/order/{id}/update', [UserController::class, 'updateOrder'])
        ->name('user.order.update')  // Changed from 'order.update'
        ->middleware('auth');
        Route::name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('home');
            Route::get('/login', [UserController::class, 'login'])->middleware("guest")->name('login.user');
            Route::get('/register', [UserController::class, 'register'])->middleware("guest")->name('register.user');

            Route::post('user/login', [UserController::class, 'loginSubmit'])->name('login.submit');
            Route::post('user/register', [UserController::class, 'registerSubmit'])->name('register.submit');
            Route::get('user/logout', [UserController::class, 'userLogout'])->name('logout');
            Route::get('/contact-us', [UserController::class, 'contactUs'])->name('contact');
            Route::get('/fqa', [UserController::class, 'fqa'])->name('fqa');
            Route::get('/shop', [UserController::class, 'shop'])->name('shop');
            Route::get('/e-vouchers', [UserController::class, 'vouchers'])->name('evouchers');
            Route::get('/card', [UserController::class, 'card'])->middleware("UserAuth")->name('card');
            Route::get('/card/data', [UserController::class, 'cardData'])->middleware("UserAuth")->name('card.data');
            Route::get('/search', [UserController::class, 'search'])->name('search');
            Route::get('product/detail/{id}', [UserController::class, 'productDetail'])->name('product.show');
            Route::get('/myorders', [UserController::class, 'myorders'])->middleware("UserAuth")->name('orders.user');
            Route::get('/myorders/{id}', [UserController::class, 'order_details'])->middleware("UserAuth")->name('order.details');
            // In web.php
            // Route::get('/order/{id}/edit', [UserController::class, 'editOrder'])
            //     ->name('order.edit')
            //     ->withoutMiddleware(['localize']);
                
            Route::get('/myvouchers', [UserController::class, 'myvouchers'])->middleware("UserAuth")->name('vochers.user');
            Route::get('/myaccount', [UserController::class, 'edit'])->middleware("UserAuth")->name('myaccount');
            Route::post('/user/update', [UserController::class, 'update'])->middleware("UserAuth")->name('myaccount.update');
            Route::get('cart', [CartController::class, 'index'])->name('cart');
            Route::post('cart/store', [CartController::class, 'store'])->name('cart.store');
            Route::post('cart/delete', [CartController::class, 'destroy'])->name('cart.delete');
            Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
            Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])->name('cart.applyDiscount');
            Route::post('/cart/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.removeDiscount');

            Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
            Route::post('/checkout/apply-discount', [CheckoutController::class, 'applyDiscount'])->name('checkout.applyDiscount');

            Route::get('/payment/paymob/callback', [CheckoutController::class, 'payWithPaymobWallet'])->name('paymob.callback');
            Route::get('/offers', [UserOfferController::class, 'index'])->name('user.offers');

            Route::get('/shipping', [UserAddressController::class, 'index'])->middleware("UserAuth")->name('shipping');
            Route::get('/shipping/create', [UserAddressController::class, 'create'])->middleware("UserAuth")->name('shipping.store');
            Route::post('/shipping/store', [UserAddressController::class, 'store'])->middleware("UserAuth")->name('useraddress.store');
            Route::get('/shipping/edit/{id}', [UserAddressController::class, 'edit'])->middleware("UserAuth")->name('shipping.edit');
            Route::post('/shipping/update', [UserAddressController::class, 'update'])->middleware("UserAuth")->name('shipping.update');
            Route::post('/shipping/destroy', [UserAddressController::class, 'destroy'])->middleware("UserAuth")->name('shipping.destroy');

            // تعديل هنا لتمكين GET و POST
            Route::match(['get', 'post'], '/vouchers/buy', [VoucherController::class, "index"])->middleware("UserAuth")->name('buy.vouchers');
            Route::post('/vouchers/buy/payment', [VoucherController::class, "payment"])->middleware("UserAuth")->name('buy.vouchers.payment');
            Route::post('/vouchers/buy/payment/manual', [VoucherController::class, "manual_payment"])->middleware("UserAuth")->name('buy.vouchers.payment.manual');
        });
    }
);

/**** payments routes ****/
Route::get('payment/{id}', [PaymentController::class, 'payment'])->name('payment-view');
Route::any('callBack', [PaymentController::class, 'callBack'])->name('verify-payment');
Route::any('vouchers/checkout', [VoucherController::class, 'callback'])->name('voucher.checkout');

Route::get('payment-success', [PaymentController::class, 'paymentSuccess'])->name("genral.payment.success");
Route::get('payment-failed', [PaymentController::class, 'paymentaFiled'])->name("genral.payment.failed");
Route::get('payment-pend', [PaymentController::class, 'paymentPend'])->name("genral.payment.pend");

Route::post("pay/manual", [CheckoutController::class, "manual_pay"])->middleware('auth')->name("manual.pay");
Route::post("pay/cards", [CheckoutController::class, "cards_pay"])->middleware('auth')->name("cards.pay");
Route::post("pay/fawry", [CheckoutController::class, "fawry_pay"])->middleware('auth')->name("fawry.pay");
Route::post("pay/fawry/wallet", [CheckoutController::class, "fawry_pay_wallet"])->middleware('auth')->name("fawry.wallet.pay");

Route::post("fawry/webhook", [PaymentController::class, "fawry_webhook"])->name("fawry.webhook");

Route::get("cronjob", [PaymentController::class, 'cronjob']);
