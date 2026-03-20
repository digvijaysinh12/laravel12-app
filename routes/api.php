<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::middleware('throttle:60,1')->get('/products',[ProductController::class, 'apiProducts']
);
