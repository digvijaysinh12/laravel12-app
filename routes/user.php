<?php

use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\OrderAnalyticsController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Customer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

Route::put('/password', [PasswordController::class, 'update'])
    ->name('password.update');

Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/category/{category}', [ProductController::class, 'categoryProducts'])
        ->whereNumber('category')
        ->name('category');
    Route::get('/export', [ProductController::class, 'export'])->name('export');
    Route::get('/{product}', [ProductController::class, 'show'])
        ->whereNumber('product')
        ->name('show');
    Route::post('/{product}/reviews', [ReviewController::class, 'store'])
        ->whereNumber('product')
        ->name('reviews.store');
    Route::delete('/{product}/reviews', [ReviewController::class, 'destroy'])
        ->whereNumber('product')
        ->name('reviews.destroy');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/increment/{id}', [CartController::class, 'increment'])->name('increment');
    Route::post('/decrement/{id}', [CartController::class, 'decrement'])->name('decrement');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'create'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
});

// Orders
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/analytics', [OrderAnalyticsController::class, 'index'])->name('analytics');
});

// Invoice (ONLY SIGNED)
Route::prefix('invoice')->name('invoice.')->group(function () {
    Route::get('/{order}/download', [OrderController::class, 'downloadSigned'])
        ->name('download');
});
