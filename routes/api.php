<?php

use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\SupportTicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])
    ->group(function () {
        Route::get('/products', [ProductController::class, 'apiProducts'])
            ->name('api.products.index');
    });

Route::post('/support-tickets', [SupportTicketController::class, 'store']);
