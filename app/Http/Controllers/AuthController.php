<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Facilitator;

class AuthController extends Controller
{
    // Show Login Page
    public function showLogin()
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
            
            // Optional: Strict Role Check
            // Map UI roles to DB roles if names differ. 
            // DB: 'facilitator', 'admin' (or 'operation_manager'?)
            // Let's assume input value is matches DB or we map it.
            // If they login as Facilitator but are Admin, maybe allow? 
            // Design implies strictness.
            
            // Simple redirect based on actual role
            $request->session()->regenerate();

            if ($user->role === 'admin' || $user->role === 'operation_manager') {
                return redirect()->route('admin.payments'); // or events.index
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
    public function showRegister()
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
        ]);

        // Create associated Facilitator profile
        Facilitator::create([
            'user_id' => $user->id,
            'join_date' => now(),
            // Other fields nullable
        ]);

        Auth::login($user);

        return redirect()->route('events.index');
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
