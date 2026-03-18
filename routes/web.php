<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;

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

Route::middleware(['auth','verified'])->group(function(){

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

});

Route::get('/products', [ProductController::class,'index'])->name('products.index');

Route::middleware(['auth','checkrole:admin'])->group(function(){

    Route::get('/products/create', [ProductController::class,'create'])->name('products.create');

    Route::post('/products', [ProductController::class,'store'])->name('products.store');

Route::get('/products/{product}/edit', [ProductController::class,'edit'])->name('products.edit');

Route::put('/products/{product}', [ProductController::class,'update'])->name('products.update');

Route::delete('/products/{product}', [ProductController::class,'destroy'])->name('products.destroy');

Route::get('/products/{product}', [ProductController::class,'show'])->name('products.show');

});

Route::get('/products/{id}', [ProductController::class,'show'])->name('products.show');


require __DIR__.'/auth.php';