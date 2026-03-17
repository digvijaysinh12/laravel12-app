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



Route::apiResource('products',ProductController::class);

require __DIR__.'/auth.php';