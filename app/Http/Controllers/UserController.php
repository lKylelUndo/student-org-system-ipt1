<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show register page
    public function showRegister()
    {
        return view('register.index');
    }

    // Handle register submission
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'min:3', 'max:100', 'regex:/^[A-Za-z\s]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6', 'numeric'],
            'confirm_password' => ['required', 'same:password'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Registration successful!');
    }

    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login submission 
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'numeric'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Email or password is incorrect'
            ]);
        }

        return back()->with('success', 'Login successful!');
    }
}