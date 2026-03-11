<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
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


Route::get('/product/{p}',function(Product $p){
    return $p;
});

Route::get('/dashboard', DashboardController::class);

// Route::get('/admin',function(){
//     return "Welcome Admin";
// })->middleware('checkrole');

Route::get('/admin', function () {
    return "Admin Panel";
})->middleware('checkrole:admin');


Route::get('/form',function(){
    return view('form');
});

Route::post('/submit-form',function(){
    return "Form Submitted";
});

Route::get('/products', [ProductController::class, 'index']);


Route::resource('products',ProductController::class);


Route::get('/res/view',[ProductController::class,'viewE']);

Route::get('/res/json', [ProductController::class,'jsonE']);

Route::get('/res/red', [ProductController::class,'redirectE']);

Route::get('/res/d', [ProductController::class,'downloadE']);

Route::get('/res/mac', [ProductController::class,'macroE']);

Route::get('/home',function(){
    return view('home');
});