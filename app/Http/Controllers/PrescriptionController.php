<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    // Store a new prescription with multiple medication items
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'date_issued'   => 'required|date',
            'dentist_id'    => 'nullable|exists:users,id',
            'notes'         => 'nullable|string',
            'medication_name'   => 'required|array|min:1',
            'medication_name.*' => 'required|string|max:255',
            'dosage.*'          => 'nullable|string|max:100',
            'frequency.*'       => 'nullable|string|max:100',
            'duration.*'        => 'nullable|string|max:100',
            'instructions.*'    => 'nullable|string',
            'quantity.*'        => 'nullable|integer|min:0',
        ]);

        $prescription = $patient->prescriptions()->create([
            'dentist_id'  => $validated['dentist_id'] ?? null,
            'date_issued' => $validated['date_issued'],
            'notes'       => $validated['notes'] ?? null,
            'status'      => 'active',
        ]);

        foreach ($validated['medication_name'] as $i => $name) {
            if (trim($name) === '') continue;

            $prescription->items()->create([
                'medication_name' => $name,
                'dosage'          => $request->input("dosage.$i"),
                'frequency'       => $request->input("frequency.$i"),
                'duration'        => $request->input("duration.$i"),
                'instructions'    => $request->input("instructions.$i"),
                'quantity'        => $request->input("quantity.$i"),
            ]);
        }

        return back()->with('success', 'Prescription saved successfully.');
    }

    // Update prescription status (active, completed, cancelled)
    public function updateStatus(Request $request, Prescription $prescription)
    {
        $request->validate([
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $prescription->update(['status' => $request->status]);

        return back()->with('success', 'Prescription updated.');
    }

    // Delete a prescription
    public function destroy(Prescription $prescription)
    {
        $prescription->delete();

        return back()->with('success', 'Prescription deleted.');
    }

    // Printable prescription slip
    public function print(Prescription $prescription)
    {
        $prescription->load('items', 'patient', 'dentist');
        $clinic = AppSetting::current();

        return view('prescriptions.print', compact('prescription', 'clinic'));
    }
}
