<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.sign_in');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(fn() => route('dashboard.index'));
        }

        return back()->withErrors([
            'title' => 'Las credenciales proporcionadas no son correctas.',
        ])->onlyInput('name');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    public function forgotPassword()
    {
        return view('auth.forgot_password');
    }
}
