<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PrivacyController extends Controller
{
    // Reveal amounts — requires password confirmation
    public function reveal(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        session(['amounts_visible' => true]);

        return back()->with('success', 'Amounts are now visible.');
    }

    // Hide amounts — instant, no password needed
    public function hide(Request $request)
    {
        session(['amounts_visible' => false]);

        return back();
    }
}