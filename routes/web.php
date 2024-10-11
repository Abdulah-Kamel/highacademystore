<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\ForgotPasswordController;
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
        Route::get("/reset-password/{token}", [ForgotPasswordController::class, "newpassform"])->middleware("guest")->name("password.reset"); // تحقق من اسم العرض هنا
        Route::post("/reset-password", [ForgotPasswordController::class, "reset"])->name("password.update")->middleware("guest");


        Route::name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('home');
            Route::get('/login', [UserController::class, 'login'])->middleware("guest")->name('login.user');
            Route::get('/register', [UserController::class, 'register'])->middleware("guest")->name('register.user');
            Route::post('user/login', [UserController::class, 'loginSubmit'])->name('login.submit');
            Route::post('user/register', [UserController::class, 'registerSubmit'])->name('register.submit');
            Route::get('user/logout', [UserController::class, 'userLogout'])->name('logout');
            Route::get('/contact-us', [UserController::class, 'contactUs'])->name('contact');
            Route::get('/shop', [UserController::class, 'shop'])->name('shop');
            Route::get('/card', [UserController::class, 'card'])->middleware("UserAuth")->name('card');
            Route::get('/search', [UserController::class, 'search'])->name('search');
            Route::get('product/detail/{id}', [UserController::class, 'productDetail'])->name('product.show');
            Route::get('/myorders', [UserController::class, 'myorders'])->middleware("UserAuth")->name('orders.user');
            Route::get('/myorders/{id}', [UserController::class, 'order_details'])->middleware("UserAuth")->name('order.details');


            Route::get('cart', [CartController::class, 'index'])->name('cart');
            Route::post('cart/store', [CartController::class, 'store'])->name('cart.store');
            Route::post('cart/delete', [CartController::class, 'destroy'])->name('cart.delete');
            Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
            Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
        });
    }
);


/**** payments routes ****/
Route::get('payment/{id}', [PaymentController::class, 'payment'])->name('payment-view');
Route::any('callBack', [PaymentController::class, 'callBack'])->name('verify-payment');


Route::get('payment-success', [PaymentController::class, 'paymentSuccess'])->name("genral.payment.success");
Route::get('payment-failed', [PaymentController::class, 'paymentaFiled'])->name("genral.payment.failed");
Route::get('payment-pend', [PaymentController::class, 'paymentPend'])->name("genral.payment.pend");

Route::post("pay/manual", [CheckoutController::class, "manual_pay"])->middleware('auth')->name("manual.pay");
Route::post("pay/cards", [CheckoutController::class, "cards_pay"])->middleware('auth')->name("cards.pay");
Route::post("pay/fawry", [CheckoutController::class, "fawry_pay"])->middleware('auth')->name("fawry.pay");

Route::get("cronjob", [PaymentController::class, 'cronjob']);

