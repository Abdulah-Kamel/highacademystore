<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\VoucherController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware'=>'api','prefix'=>'auth'],function($router) {
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/admin/edit', [AuthController::class,'updateAdmin']);

    Route::get('/product',[AuthController::class,'index']);
    Route::post('/product/create',[AuthController::class,'store']);

    Route::get('/category',[AuthController::class,'category']);
    Route::get('/subcategory',[AuthController::class,'subcategory']);
    Route::get('/brand',[AuthController::class,'brand']);


});

Route::any("webhook/fawry", [PaymentController::class, "fawry_webhook"])->name("fawry.webhook");
Route::any("webhook/fawry/vouchers", [VoucherController::class, "fawry_webhook"])->name("vouchers.webhook");