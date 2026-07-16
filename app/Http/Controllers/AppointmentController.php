<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentReschedule;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // List upcoming appointments, grouped by date, optionally filtered by dentist
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'dentist'])
            ->where('appointment_date', '>=', today())
            ->whereNotIn('status', ['cancelled']);

        $user = auth()->user();
        $filterDentistId = $request->input('dentist_id');

        if ($filterDentistId) {
            $query->where('dentist_id', $filterDentistId);
        } elseif ($user->isDentist() && !$request->boolean('view_all')) {
            $query->where('dentist_id', $user->id);
        }

        $appointments = $query->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(fn ($appt) => $appt->appointment_date->format('Y-m-d'));

        $dentists = User::dentists()->orderBy('name')->get();
        $patients = Patient::orderBy('last_name')->get();

        return view('appointments.index', compact('appointments', 'dentists', 'patients', 'filterDentistId'));
    }

    // Show the booking form
    public function create()
    {
        $patients = Patient::orderBy('last_name')->get();
        $dentists = User::dentists()->orderBy('name')->get();
        return view('appointments.create', compact('patients', 'dentists'));
    }

    // Save a new appointment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'dentist_id' => 'nullable|exists:users,id',
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

    // Show the edit/reschedule form
    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('last_name')->get();
        $dentists = User::dentists()->orderBy('name')->get();
        return view('appointments.edit', compact('appointment', 'patients', 'dentists'));
    }

    // Update an appointment (general edit - patient, dentist, purpose, notes)
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'dentist_id' => 'nullable|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Detect if date or time actually changed - if so, treat as a reschedule
        $dateChanged = $appointment->appointment_date->format('Y-m-d') !== $validated['appointment_date'];
        $timeChanged = $appointment->appointment_time !== $validated['appointment_time'];

        if ($dateChanged || $timeChanged) {
            AppointmentReschedule::create([
                'appointment_id'  => $appointment->id,
                'old_date'        => $appointment->appointment_date,
                'old_time'        => $appointment->appointment_time,
                'new_date'        => $validated['appointment_date'],
                'new_time'        => $validated['appointment_time'],
                'reason'          => $request->input('reschedule_reason'),
                'rescheduled_by'  => auth()->id(),
            ]);

            $appointment->reschedule_count += 1;
        }

        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', $dateChanged || $timeChanged ? 'Appointment rescheduled successfully.' : 'Appointment updated.');
    }

    // Delete an appointment entirely
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}
