<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        unset($data['password_confirmation']);
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('posts.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'بيانات الدخول غير صحيحة'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $request->user();

        return redirect()->route(
            in_array($user->role, [Role::Admin, Role::Author], true)
                ? 'dashboard.posts.index'
                : 'posts.index'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('posts.index');
    }
}
