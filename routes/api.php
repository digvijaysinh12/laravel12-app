<?php

use App\Http\Controllers\Customer\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])
    ->group(function () {
        Route::get('/products', [ProductController::class, 'apiProducts'])
            ->name('api.products.index');
    });
