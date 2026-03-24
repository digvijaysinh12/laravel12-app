<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});



/*
|--------------------------------------------------------------------------
| Dashboard (Authenticated Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->whereNumber('product')
        ->name('products.show');

    Route::get('/cart',[CartController::class,'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
});




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

require __DIR__ . '/auth.php';
