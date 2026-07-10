<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // Show list of all patients
    public function index()
    {
        $patients = Patient::orderBy('last_name')->get();
        return view('patients.index', compact('patients'));
    }

    // Show the form to add a new patient
    public function create()
    {
        return view('patients.create');
    }

    // Save the new patient to the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'sex' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string',
            'referred_by' => 'nullable|string',
            'reason_for_consultation' => 'nullable|string',
            'previous_dentist' => 'nullable|string',
            'last_dental_visit' => 'nullable|date',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'medications' => 'nullable|string',
            'physician_name' => 'nullable|string',
            'physician_specialty' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'blood_pressure' => 'nullable|string',
            'medical_conditions_checklist' => 'nullable|array',
        ]);

        Patient::create($validated);

        return redirect()->route('patients.index')->with('success', 'Patient added successfully.');
    }

    // Show one patient's profile + history
    public function show(Patient $patient)
    {
        $logs = $patient->logs;
        return view('patients.show', compact('patient', 'logs'));
    }

    // Save a new log entry (visit, payment, or note) for a patient
    public function storeLog(Request $request, Patient $patient)
    {
$validated = $request->validate([
            'visit_date' => 'required|date',
            'tooth_number' => 'nullable|string|max:50',
            'entry_type' => 'required|in:visit,payment,note',
            'description' => 'nullable|string',
            'amount_charged' => 'nullable|numeric|min:0',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

$validated['amount_charged'] = $validated['amount_charged'] ?? 0;
        $validated['amount_paid'] = $validated['amount_paid'] ?? 0;
        $validated['description'] = $validated['description'] ?: 'Balance payment';
        $validated['recorded_by'] = auth()->user()->name;

        $patient->logs()->create($validated);

// Update the patient's running balance (never goes below zero)
        $newBalance = $patient->balance + $validated['amount_charged'] - $validated['amount_paid'];
        $patient->balance = max(0, $newBalance);
        $patient->save();

        return redirect()->route('patients.show', $patient)->with('success', 'Entry logged successfully.');
    }
}