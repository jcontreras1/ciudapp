<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


class AuthController extends Controller
{
    public function register() {
        return Inertia::render('User/Register');
    }

    public function store(Request $request) {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        
        auth()->login($user);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function login() {
        return Inertia::render('Auth/Login',
            [
                'canResetPassword' => Route::has('password.request'),
            ]
        );
    }

    public function auth(Request $request) {
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if(auth()->attempt([
            'password' => $request->password,
            'email' => $request->email,
        ])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }else {
            return Inertia::render('Auth/Login', [
                'status' => "Esas credenciales no coinciden con nuestros registros."
            ])->with('error', 'Esas credenciales no coinciden con nuestros registros.');
        }
    }

    public function logout(Request $request) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function emailVerification() {
        if(auth()->user()->hasVerifiedEmail()) {
            return redirect()->back();
        }
        return Inertia::render('User/VerifyEmail');
    }

    public function forgotPassword() {
        return Inertia::render('Auth/ForgotPassword');
    }
}
