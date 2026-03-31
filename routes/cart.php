<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('cart')->group(function () {

    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/increment/{id}', [CartController::class, 'increment']);
    Route::post('/decrement/{id}', [CartController::class, 'decrement']);
    Route::delete('/remove/{id}', [CartController::class, 'remove']);

});