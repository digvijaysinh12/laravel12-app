<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'checkrole:admin'])->group(function () {

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->whereNumber('product')
        ->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->whereNumber('product')
        ->name('products.update');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->whereNumber('product')
        ->name('products.destroy');
});