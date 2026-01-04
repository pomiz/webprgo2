<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\Produk;
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

    Route::get('/cart', [UserController::class, 'cart'])->name('cart.view');
    Route::get('/add-to-cart/{id}', [UserController::class, 'addToCart'])->name('cart.add');
    Route::get('/remove-from-cart/{id}', [UserController::class, 'removeFromCart'])->name('cart.remove');

});

// 🔐 Breeze auth routes
require __DIR__.'/auth.php';// End of file
