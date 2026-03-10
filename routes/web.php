<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/config',[CompanyController::class, 'showCompany' ]);

Route::get('/discount', [ProductController::class, 'discount']);

Route::get('/pay',[PaymentController::class,'pay']);

Route::get('/faced-test', function(){
    Log::info("Faced");

    Cache::put("name","Digvijaysinh",60);

    $name = Cache::get('name');

    $users = DB::table('users')->get();

    $files = File::files(storage_path());

    return [
        'cached_name' => $name,
        'users_count' => $users->count(),
        'files_count' => count($files)
    ];

});