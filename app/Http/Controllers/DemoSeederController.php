<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DemoSeederController extends Controller
{
    // Wipes real patient/appointment/prescription data and loads demo data.
    // Clinic branding, theme, and login accounts are untouched.
    public function run(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|in:RESET DATA',
        ]);

        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\DemoDataSeeder',
            '--force' => true,
        ]);

        return back()->with('success', 'Demo data loaded. All previous patient records were replaced.');
    }
}
