<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // List upcoming appointments, grouped by date
    public function index()
    {
        $appointments = Appointment::with('patient')
            ->where('appointment_date', '>=', today())
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(fn ($appt) => $appt->appointment_date->format('Y-m-d'));

        return view('appointments.index', compact('appointments'));
    }

    // Show the booking form
    public function create()
    {
        $patients = Patient::orderBy('last_name')->get();
        return view('appointments.create', compact('patients'));
    }

    // Save a new appointment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Appointment::create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
    }

    // Update just the status (e.g. mark as completed, no-show, cancelled)
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,confirmed,completed,no_show,cancelled',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return back()->with('success', 'Appointment updated.');
    }
}