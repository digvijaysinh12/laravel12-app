<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

// Public routes
Route::get('/test-string', fn () => "Hello Intern");
Route::get('/test-array', fn () => ['name' => "Digvijaysinh"]);
Route::get('/test-json', fn () => response()->json(['status' => 'true']));

Route::get('/download-file', function () {
    $path = storage_path('app/public/products/sample.png');
    return response()->download($path);
});

// Signed URL
Route::get('/unsubscribe/{user}', function ($user) {
    return "Unsubscribed user: " . $user;
})->name('unsubscribe')->middleware('signed');

Route::get('/test-signed', function () {
    return URL::signedRoute('unsubscribe', ['user' => 1]);
});

// Redis test
Route::get('/test-redis', function () {
    Redis::set('test_key', 'Hello Redis');
    return Redis::get('test_key');
});

// Auth routes
require __DIR__.'/auth.php';

// 🔥 MAIN AUTH GROUP
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modular routes
    require __DIR__.'/product.php';
    require __DIR__.'/cart.php';
    require __DIR__.'/checkout.php';
    require __DIR__.'/invoice.php';
    require __DIR__.'/admin.php';

});

// Fallback
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});