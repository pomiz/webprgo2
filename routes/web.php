<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Semua route utama aplikasi toko baju online
|
*/

// 🌟 WELCOME PAGE (PUBLIC)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 🛍️ SHOP (WAJIB LOGIN)
Route::middleware('auth')->group(function () {

    Route::get('/product', [UserController::class, 'index'])->name('home');

    Route::get('/product/{id}', [UserController::class, 'show'])->name('product.detail');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout route
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/invoice/{order}', [CheckoutController::class, 'invoice'])->name('invoice.show');

});

// 🔐 Breeze auth routes
require __DIR__.'/auth.php';// End of file