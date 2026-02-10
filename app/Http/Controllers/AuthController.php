<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Show Login Page
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|string' 
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Strict Role Validation: Check if selected role matches user's actual role
            if ($user->role !== $request->role) {
                Auth::logout();
                return back()->withErrors([
                    'role' => 'Access denied. You are registered as a ' . ucfirst(str_replace('_', ' ', $user->role)) . ', not a ' . ucfirst(str_replace('_', ' ', $request->role)) . '.',
                ])->withInput();
            }
            
            // Simple redirect based on actual role
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.payments'); 
            }
             if ($user->role === 'operation_manager') {
                return redirect()->route('dashboard'); 
            }
            if ($user->role === 'marketing_manager') {
                return redirect()->route('events.index'); // or events.index
            }
            
            return redirect()->route('events.index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput(); // Keep input except password
    }

    // Show Register Page
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'facilitator', // Design says "Register as Facilitator"
            'joinDate' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('login');
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
