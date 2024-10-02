<?php

use App\Http\Controllers\api\ApiPostController;
use App\Http\Controllers\Api\CityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {

    
    
    Route::get('/cities', [CityController::class, 'search']);
    Route::post('/post/{post}/comment', [ApiPostController::class, 'store']);
    Route::post('/post/{post}/like', [ApiPostController::class, 'like']);

});