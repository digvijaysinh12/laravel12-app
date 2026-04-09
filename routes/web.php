<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Customer\ProductController;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', [HomePageController::class, 'index'])->name('home');

Route::middleware(['auth', 'checkrole:user'])
    ->get('/dashboard', [ProductController::class, 'featured'])
    ->name('dashboard');

require __DIR__.'/auth.php';

// Authenticated customer area
Route::middleware(['auth', 'checkrole:user'])->name('user.')->group(function () {
    require __DIR__.'/user.php';
});

// Admin area
Route::middleware(['auth', 'checkrole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__.'/admin.php';
    });

Route::fallback([PageController::class, 'notFound']);
