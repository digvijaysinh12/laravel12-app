<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Middleware\SetUserContext;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', [HomePageController::class, 'index'])->name('home');

Route::middleware(['auth', 'checkrole:user'])
    ->get('/dashboard', [ProductController::class, 'featured'])
    ->name('dashboard');

require __DIR__.'/auth.php';

// Authenticated customer area
Route::middleware(['auth', 'checkrole:user', SetUserContext::class])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        require __DIR__.'/user.php';
    });

// Admin area
Route::middleware(['auth', 'checkrole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__.'/admin.php';
    });

Route::middleware('auth')->group(function () {
    // FIXED: shared notification API for both admin and users.
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

Route::fallback([PageController::class, 'notFound']);
