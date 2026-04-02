<?php

use App\Http\Controllers\Admin\AdminOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Only accessible by authenticated users with admin role
*/

Route::middleware(['auth', 'checkrole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard (optional)
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Product Management
        Route::prefix('products')->name('products.')->group(function () {

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

    });