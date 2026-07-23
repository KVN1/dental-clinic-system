<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    // The tour sequence: step number => [route to visit, title, description]
    public static function steps(): array
    {
        return [
            1 => [
                'route' => 'dashboard',
                'title' => 'Your Dashboard',
                'desc'  => "This is your home base. See today's appointments, outstanding balances, and reminders every time you log in.",
            ],
            2 => [
                'route' => 'patients.index',
                'title' => 'Patients',
                'desc'  => 'Add new patients, search existing ones, and manage their medical history, treatments, prescriptions, and X-rays all in one place.',
            ],
            3 => [
                'route' => 'appointments.index',
                'title' => 'Appointments',
                'desc'  => 'Book appointments manually, or click any date on the calendar below to schedule directly. Assign a dentist and it color-codes automatically.',
            ],
            4 => [
                'route' => 'settings.index',
                'title' => 'Settings',
                'desc'  => 'This is where you brand the system as your own: clinic name, logo, colors, staff accounts, and backups.',
            ],
        ];
    }

    // Move to the next step and redirect to that page
    public function next(Request $request)
    {
        $current = (int) session('onboarding_step', 1);
        $steps = self::steps();
        $next = $current + 1;

        if (!isset($steps[$next])) {
            // Tour finished
            session()->forget('onboarding_step');
            return redirect()->route('dashboard')->with('success', "You're all set! Explore freely, or revisit the User Manual anytime.");
        }

        session(['onboarding_step' => $next]);
        return redirect()->route($steps[$next]['route']);
    }

    // Skip the rest of the tour entirely
    public function skip()
    {
        session()->forget('onboarding_step');
        return redirect()->route('dashboard');
    }

    // Restart the tour from Settings (optional "Replay Tour" button)
    public function restart()
    {
        session(['onboarding_step' => 1]);
        return redirect()->route('dashboard');
    }
}
