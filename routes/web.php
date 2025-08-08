<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\PostController;
use App\Models\Preference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('posts', App\Http\Controllers\PostController::class);
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/test2', function () {
    return \App\Http\Controllers\Services\GetBoundsFromAddressesService::getbounds(
        'san martin',
        'marcos a zar',
        'moreno',
        'rivadavia'
    );
});
Route::get('/test', function () {
    $url = \App\Http\Controllers\Services\GetVertexFromStreetsService::getVertex('san martin', 'gobernador maiz');
    return $url;
});
 
Route::get('/google-auth/callback', function () {
    $userGoogle = Socialite::driver('google')->stateless()->user();
    $user = User::updateOrCreate(
        [
            // Aquí verificamos primero por el email (que debería ser único), y luego actualizamos o creamos el google_id
            'email' => $userGoogle->email
        ],
        [
            'google_id' => $userGoogle->id,
            'name' => $userGoogle->name,
        ]
    );
    Auth::login($user, true);
    return redirect()->route('home');
    // $user->token
});
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function () {

        Route::resource('/institution/{institution}/incidents', IncidentController::class)->only(['index', 'show']);

        Route::get('profile', function () {
            $userPreferences = auth()->user()->preferences;
            $preferences = Preference::all();
            return Inertia::render('Profile/Show', [
                'preferences' => $preferences,
                'userPreferences' => $userPreferences ?? []
            ]);
        })->name('profile');

        Route::get('profile/updatePassword', function () {
            return Inertia::render('Profile/Partials/UpdatePasswordForm');
        })->name('profile.updatePassword');

        Route::get('/bounds', [PostController::class, 'bounds'])->name('posts.bounds');

        Route::get('/dashboard', function () {
            return redirect()->route('home');
        })->name('dashboard');
        Route::resource('category', App\Http\Controllers\CategoryController::class)->except('show');
        Route::get('relationships', [App\Http\Controllers\CategoryController::class, 'relationships'])->name('relationships');
        Route::resource('category/{category}/subcategory', App\Http\Controllers\SubcategoryController::class)->except('show');
        Route::resource('post/{post}/comment', App\Http\Controllers\CommentController::class)->only(['index', 'store', 'update']);
 
        Route::resource('institution', App\Http\Controllers\InstitutionController::class)->except('show');
        Route::resource('institution/{institution}/region', App\Http\Controllers\RegionController::class)->except('show');
        Route::resource('institution/{institution}/userInstitution', App\Http\Controllers\UserInstitutionController::class)->only(['store', 'destroy', 'update']);
    });
//Auth::routes();

include('auth.php');

Route::get('/ask', function(){
    return Inertia::render('OpenAI/Ask');
});

Route::get('/testing', function () {
    $institution = App\Models\Institution::with('regions')->find(2);    
    return Inertia::render('Institucion/Reports', ['institution' => $institution]);
});
