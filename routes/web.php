<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('posts', App\Http\Controllers\PostController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function () {


        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
        Route::resource('category', App\Http\Controllers\CategoryController::class)->except('show');
        Route::resource('category/{category}/subcategory', App\Http\Controllers\SubcategoryController::class)->except('show');
        Route::resource('post/{post}/comment', App\Http\Controllers\CommentController::class)->only(['index', 'store', 'update']);
        
    });
    
    Route::resource('institution', App\Http\Controllers\InstitutionController::class);
//Auth::routes();

include('auth.php');