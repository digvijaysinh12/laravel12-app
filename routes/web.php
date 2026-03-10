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


Route::get('/greet', function(){
    return Greeting::hello("Digvijaysinh");
});


Route::get('/Get', function () {
    return "Hello From Get Route";
});

Route::post('/submit', function () {
    return "Form submitted successfully";
});

Route::get('/user/{name}', function ($name) {
    return "User: " . $name;
});

Route::get('/profile/{name?}', function ($name = "Guest") {
    return "Profile: " . $name;
});

Route::get('/dashboard', function () {
    return "Dashboard Page";
})->name('dashboard');

Route::redirect('/home', '/dashboard');


Route::prefix('admin')->group(function () {

    Route::get('/users', function () {
        return "Admin Users";
    });

    Route::get('/settings', function () {
        return "Admin Settings";
    });


});

Route::fallback(function () {
    return "Page Not Found";
});

