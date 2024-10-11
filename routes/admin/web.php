<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StageController;
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

            // Product Section
            Route::get('/products', [ProductController::class, 'index'])->name('product');
            Route::get('/products/create', [ProductController::class, 'create'])->name('create.product');
            Route::get('product/datatable', [ProductController::class, 'datatable'])->name('product.datatable');
            Route::post('/store/products', [ProductController::class, 'store'])->name('store.product');
            Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
            Route::post('product/update', [ProductController::class, 'update'])->name('product.update');
            Route::post('product/destroy', [ProductController::class, 'destroy'])->name('product.destroy');

            //order
            Route::get('/orders', [OrderController::class, 'index'])->name('orders');
            Route::get('orders/datatable', [OrderController::class, 'datatable'])->name('orders.datatable');
            Route::get('orders/details/{id}', [OrderController::class, 'details'])->name('orders.details');
            Route::post("order/change-state", [OrderController::class, "changestate"])->name("changestate");

        });
    }
);

