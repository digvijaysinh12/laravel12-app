<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\SalesAnalyticsController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])
        ->name('index');

    Route::get('/export', [ProductController::class, 'export'])
        ->name('export');

    Route::get('/create', [ProductController::class, 'create'])
        ->name('create');
    Route::post('/', [ProductController::class, 'store'])
        ->name('store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])
        ->whereNumber('product')
        ->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])
        ->whereNumber('product')
        ->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])
        ->whereNumber('product')
        ->name('destroy');
});

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])
        ->name('index');
    Route::get('/{order}', [AdminOrderController::class, 'show'])
        ->name('show');
    Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('status');
});

Route::view('/users', 'admin.users.index')
    ->name('users.index');

        Route::get('/sales-analytics', [SalesAnalyticsController::class, 'index'])
        ->name('admin.sales.analytics');

    Route::get('/sales-analytics/export', [SalesAnalyticsController::class, 'export'])
        ->name('admin.sales.export');