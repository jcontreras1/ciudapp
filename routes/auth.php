                                                            
                                                                                                                                <?php

use App\Http\Controllers\Auth\AuthController;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function() {

    // Route::post('user/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/email/verify', [AuthController::class, 'emailVerification'])->middleware('auth')->name('verification.notice');
   
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
     
        return redirect()->route('home')->with([
            'message' => 'Email verified successfully',
            'class' => 'alert alert-success'
        ]);
    })->middleware('signed')->name('verification.verify');
    
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
     
        return redirect()->back()->with([
            'message' => 'Verification link sent!',
            'class' => 'alert alert-success'
        ]);
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware('guest')->group(function() {
    Route::get('user/register', [AuthController::class, 'register'])->name('register.show');
    
    Route::post('user/register', [AuthController::class, 'store'])->name('register.store');
    
    // Route::get('user/login', [AuthController::class, 'login'])->name('login1');
    
    // Route::post('user/login', [AuthController::class, 'auth'])->name('login2');

    Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
});
                                                            
                                                        