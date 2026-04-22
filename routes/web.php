<?php

use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Middleware\SetUserContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', [HomePageController::class, 'index'])->name('home');

Route::get('/http-client',function(){
        $response = Http::get('https://fakestoreapi.com/products');

        $products = $response->collect();

        // dd($products);

        return view('api', compact('products'));
});

Route::get('http-post', function () {

    $response = Http::post('https://fakestoreapi.com/products', [
        "id" => 10092387,
        "title" => "Digvijaysinh Sarvaiyas"
    ]);

    if ($response->successful()) {
        Log::channel('products')->info('Product created successfully', [
            'data' => $response->json()
        ]);

        return $response->json();
    }

    if ($response->clientError()) {
        Log::channel('products')->warning('Client error while creating product', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return response()->json(['error' => 'Client error'], 400);
    }

    if ($response->serverError()) {
        Log::channel('products')->error('Server error from API', [
            'status' => $response->status()
        ]);

        return response()->json(['error' => 'Server error'], 500);
    }
});


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
