<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SalesAnalyticsController;
use App\Http\Controllers\Admin\CacheMonitorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('categories', CategoryController::class)->except(['show']);

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/export', [ProductController::class, 'export'])->name('export');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->whereNumber('product')->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->whereNumber('product')->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->whereNumber('product')->name('destroy');
});

Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [AdminOrderController::class, 'index'])->name('index');
    Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
    Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('status');
});

Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/{customer}', [CustomerController::class, 'show'])->whereNumber('customer')->name('show');
});

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');

Route::get('/reports', [SalesAnalyticsController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [SalesAnalyticsController::class, 'export'])->name('reports.export');
Route::get('/reports/download/{file}', [ReportController::class, 'download'])
    ->name('reports.download');

Route::prefix('cache')->name('admin.cache.')->group(function () {

    Route::get('/', [CacheMonitorController::class, 'index'])
        ->name('index');

    Route::post('/clear', [CacheMonitorController::class, 'clear'])
        ->name('clear');

    Route::post('/clear-tag/{tag}', [CacheMonitorController::class, 'clearTag'])
        ->name('clearTag');

});

Route::view('/logs', 'admin.logs.index')->name('logs.index');
