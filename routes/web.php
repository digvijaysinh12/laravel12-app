<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\URL;

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products (View only)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->whereNumber('product')
        ->name('products.show');

        Route::get('/products/export',[ProductController::class, 'export'])->name('products.export');


    // Cart
    Route::get('/cart',[CartController::class,'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/increment/{id}', [CartController::class, 'increment']);
    Route::post('/cart/decrement/{id}', [CartController::class, 'decrement']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);

    Route::post('/checkout', [CheckoutController::class, 'store'])
    ->name('checkout');

    Route::get('/invoice/{order}', [InvoiceController::class, 'show'])
        ->name('invoice.show');

});

Route::get('/test-string', function () {
    return "Hello Intern";
});

Route::get('/test-array', function () {
    return ['name' => "Digvijaysinh"];
});

Route::get('/test-json', function () {
    return response()->json(['status' => 'true']);
});

Route::get('/download-file', function () {
    $path = storage_path('app/public/products/7nBHWQhO9SR2nNgMZKGeP3nkqVonQWM4zMqBodoL.png');
    return response()->download($path);
});

Route::get('/unsubscribe/{user}',function($user){
    return "Unsubscribed user: " . $user;
})->name('unsubscribe')->middleware('signed');

Route::get('/test-signed',function(){
    return URL::signedRoute('unsubscribe',['user' =>1]);
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/test-redis', function () {
    Redis::set('test_key', 'Hello Redis');
    return Redis::get('test_key');
});
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/product.php';
