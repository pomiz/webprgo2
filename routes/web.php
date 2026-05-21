<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductPrintController;
use App\Http\Controllers\Admin\UserPrintController;
use App\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Semua route utama aplikasi toko baju online
|
*/

// Group Route yang Wajib Login (Auth)
Route::middleware(['auth'])->group(function () {

    // 🏠 Halaman utama - Cek role admin atau user biasa
    Route::get('/', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('filament.admin.pages.dashboard');
        }
        return app(App\Http\Controllers\UserController::class)->index(request());
    })->name('home');

    // 🛍️ Detail produk
    Route::get('/product/{id}', [UserController::class, 'show'])->name('product.detail');

    // 📦 Product list
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // 🛒 Fitur keranjang
    Route::get('/cart', [UserController::class, 'cart'])->name('cart.index');
    Route::post('/add-to-cart/{id}', [UserController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{id}', [UserController::class, 'removeFromCart'])->name('cart.remove');

    // 💳 Checkout
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/invoice/{order}', [CheckoutController::class, 'invoice'])->name('invoice.show');

    // 👤 Route profile bawaan auth
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// These routes need auth protection
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('filament.admin.pages.dashboard');
    })->name('dashboard');

    Route::get('/print/products', ProductPrintController::class)->name('print.product');
    Route::get('/print/users', UserPrintController::class)->name('print.user');
});

// 🔐 Breeze auth routes
require __DIR__.'/auth.php';