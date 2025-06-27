<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:6',
        ]);
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('home.index')->with('success', 'Welcome back! 📸 ');
        }
        return view('auth.login');
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('home.index');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'phone' => 'required|digits_between:8,15|unique:users,phone',
            'password' => 'required|string|min:6|confirmed', 
        ]);

        $user = User::create([
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'streak' => 0,
        ]);

        Auth::login($user);

        return redirect()->route('home.index')->with('success', 'Welcome to OneTap! 📸');
    }


}
