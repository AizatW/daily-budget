<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function showRegister(){
        return view('auth.register');
    }

    public function register(RegisterRequest $request){
        $validated = $request->validated();
    

        User::create($validated);

        return redirect('/login')
            ->with('success', 'Account successfully created. Please log in.');
    }

    public function showLogin(){
        return view('auth.login');
    }

    public function login(LoginRequest $request){
        $validated = $request->validated();

        if (Auth::attempt($validated)){
            $request->session()->regenerate();

            return redirect()->intended('/expenses');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password',
        ])->onlyInput('email');
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
