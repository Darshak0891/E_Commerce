<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
  
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('admin')->group( function () {
    Route::resource('categories', CategoryController::class);

    Route::get('/products/index',[ProductController::class, 'index']);
    Route::post('/products/store',[ProductController::class, 'store']);
    Route::patch('/products/show/{id}',[ProductController::class, 'show']);
    Route::post('/products/update/{id}',[ProductController::class, 'update']);
    Route::delete('/products/delete/{id}',[ProductController::class, 'delete']);

});

Route::middleware('user')->group( function () {
    
});


