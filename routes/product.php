<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Product Routes (User Side)
|--------------------------------------------------------------------------
| Accessible by authenticated users
*/

Route::prefix('products')
    ->name('products.')
    ->group(function () {

        // List all products
        Route::get('/', [ProductController::class, 'index'])
            ->name('index');

        // Export products (CSV/Excel)
        Route::get('/export', [ProductController::class, 'export'])
            ->name('export');

        // Show single product
        Route::get('/{product}', [ProductController::class, 'show'])
            ->whereNumber('product')
            ->name('show');

    });