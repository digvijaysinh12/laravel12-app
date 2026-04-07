<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'featured'])->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->name('user.')->group(function () {
    require __DIR__.'/user.php';
});

Route::middleware(['auth', 'checkrole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__.'/admin.php';
    });

Route::fallback(fn () => response()->view('errors.404', [], 404));
