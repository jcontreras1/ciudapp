<?php

use App\Http\Controllers\api\ApiCommentLikeController;
use App\Http\Controllers\api\ApiPostController;
use App\Http\Controllers\api\CityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/post/{post}', [ApiPostController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {

    //Social
    Route::post('/post/{post}/comment', [ApiPostController::class, 'storeComment']);
    Route::post('/post/{post}/like', [ApiPostController::class, 'like']);
    Route::resource('/comment/{post_comment}/like', ApiCommentLikeController::class)->only(['store', 'destroy']);
    //App
    Route::delete('/post/{post}', [ApiPostController::class, 'destroy']);
    Route::get('/cities', [CityController::class, 'search']);
    Route::post('/post', [ApiPostController::class, 'store']);

    //Geolocalizacion
    Route::get('/geocoding', function(Request $request) {
        $url = "https://api.mapbox.com/search/geocode/v6/reverse?longitude=$request->lng&latitude=$request->lat&access_token=" . config('app.mapbox_api_key');
        $response = Http::get($url);
        return $response->json();
    });
});