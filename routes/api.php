<?php

use App\Events\NewPostEvent;
use App\Http\Controllers\api\ApiCommentLikeController;
use App\Http\Controllers\api\ApiIncidentCommentController;
use App\Http\Controllers\api\ApiIncidentController;
use App\Http\Controllers\api\CityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Resources\IncidentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiPostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/post/{post}', [ApiPostController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {

    //Social
    Route::post('/post/{post}/comment', [ApiPostController::class, 'storeComment']);
    Route::put('/post/{post}/comment/{comment}/actualizar', [ApiPostController::class, 'updateComment']);
    
    Route::post('/post/{post}/like', [ApiPostController::class, 'like']);
    Route::resource('/comment/{post_comment}/like', ApiCommentLikeController::class)->only(['store', 'destroy']);
    Route::delete('/comment/{comment}/delete', [ApiPostController::class, 'dropComment']);

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
    Route::post('/region/{region}/region_subcategory/all', [App\Http\Controllers\api\ApiRegionSubcategoryController::class, 'all'])->name('subcategory.all');
    Route::delete('/region/{region}/region_subcategory/allDestroy', [App\Http\Controllers\api\ApiRegionSubcategoryController::class, 'destroyAll'])->name('subcategory.destroyAll');
    Route::resource('region/{region}/region_subcategory', App\Http\Controllers\api\ApiRegionSubcategoryController::class)->only(['store', 'destroy']);
    Route::resource('regionSubcategory/{regionSubcategory}/userRegionSubcategory', App\Http\Controllers\api\ApiUserRegionSubcategoryController::class)->only(['index', 'store', 'destroy']);
    Route::get('institution/{institution}/reports', [App\Http\Controllers\api\ApiInstitutionController::class, 'reports' ]);
    Route::get('institution/{institution}/subcategories', [App\Http\Controllers\api\ApiInstitutionController::class, 'subcategories' ]);

    //alternar alguna preferencia del usuario
    Route::post('/user/preference/toggle', [App\Http\Controllers\api\ApiPreferenceController::class, 'toggle']);

    Route::post('/region/{region}/region_subcategory/all', [App\Http\Controllers\api\ApiRegionSubcategoryController::class, 'all'])->name('subcategory.all');
    Route::delete('/region/{region}/region_subcategory/allDestroy', [App\Http\Controllers\api\ApiRegionSubcategoryController::class, 'destroyAll'])->name('subcategory.destroyAll');
    Route::resource('region/{region}/region_subcategory', App\Http\Controllers\api\ApiRegionSubcategoryController::class)->only(['store', 'destroy']);
    Route::get('/heatmap-data', [PostController::class, 'pruebaMapaCalor'])->name('mapacalor');
    Route::resource('regionSubcategory/{regionSubcategory}/userRegionSubcategory', App\Http\Controllers\api\ApiUserRegionSubcategoryController::class)->only(['index', 'store', 'destroy']);

    //Incidentes
    Route::resource('institution/{institution}/incident', ApiIncidentController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::post('institution/{institution}/incident/{incident}/comment', [ApiIncidentCommentController::class, 'store'])->name('incident.comment.store');
    Route::delete('institution/{institution}/incident/{incident}/comment/{comment}', [ApiIncidentCommentController::class, 'destroy'])->name('incident.comment.destroy');
    Route::post('institution/{institution}/incident/{incident}/status', [ApiIncidentController::class, 'changeStatus']);
    Route::post('institution/{institution}/post/{post}/makeIncident', [ApiIncidentController::class, 'makeIncidentFromPost']);
    Route::post('institution/{institution}/incident/{incident}/addPosts', [ApiIncidentController::class, 'addPostsToIncident']);
    Route::post('institution/{institution}/incident/{incident}/post/{post}/remove', [ApiIncidentController::class, 'removePostFromIncident']);
    Route::get('institution/{institution}/incident/{incident}/post/{post}/nearby', [ApiIncidentController::class, 'nearby']);
});