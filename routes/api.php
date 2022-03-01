<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::namespace('Api')->group(function (){
    Route::post('/products' , [ProductController::class,'store']);
    Route::get('/product/{id}' , [ProductController::class,'show']);
    Route::put('/product/{id}' , [ProductController::class,'update']);
    Route::delete('/product/{id}' , [ProductController::class,'delete']);
});

