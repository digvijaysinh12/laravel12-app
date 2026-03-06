<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/config',[CompanyController::class, 'showCompany' ]);

Route::get('/discount', [ProductController::class, 'discount']);