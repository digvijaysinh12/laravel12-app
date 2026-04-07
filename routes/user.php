<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\OrderAnalyticsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', fn () => view('user.dashboard'))
    ->name('dashboard');

Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');

Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');

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
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/increment/{id}', [CartController::class, 'increment'])->name('increment');
    Route::post('/decrement/{id}', [CartController::class, 'decrement'])->name('decrement');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
});

Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout');

Route::get('/orders/realtime-example', function () {
    return view('user.orders.realtime-example');
})->name('orders.realtime-example');

Route::get('/orders/analytics', [OrderAnalyticsController::class, 'index'])
    ->name('orders.analytics');

Route::prefix('invoice')->name('invoice.')->group(function () {
    Route::get('/', function () {
        $invoice = session('last_invoice');

        if (!$invoice) {
            return redirect()->route('user.cart.index')
                ->with('error', 'No invoice found');
        }

        return view('user.invoice.index', compact('invoice'));
    })->name('show');
    Route::get('/pdf', [CheckoutController::class, 'downloadPdf'])
        ->name('pdf');
});
