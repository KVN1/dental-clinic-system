<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class FirstTimeSetupController extends Controller
{
    // Show the setup form - only accessible if no users exist yet
    public function show()
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return view('auth.first-time-setup');
    }

    // Create the first admin account
    public function store(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'admin',
            'is_active' => true,
        ]);

        Auth::login($user);

        session(['onboarding_step' => 1]);

        return redirect()->route('dashboard')->with('success', 'Welcome! Your admin account has been created.');
    }
}
