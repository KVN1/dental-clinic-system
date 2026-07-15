<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientImageController extends Controller
{
    // Upload one or more images/x-rays for a patient
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'images'        => 'required|array|min:1',
            'images.*'      => 'image|mimes:jpg,jpeg,png,webp,gif|max:10240', // 10MB max per file
            'type'          => 'required|in:xray,photo,document,other',
            'label'         => 'nullable|string|max:255',
            'taken_date'    => 'nullable|date',
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('patient-images/' . $patient->id, 'public');

            $patient->images()->create([
                'uploaded_by' => auth()->id(),
                'file_path'   => $path,
                'type'        => $validated['type'],
                'label'       => $validated['label'] ?? null,
                'taken_date'  => $validated['taken_date'] ?? now(),
            ]);
        }

        return back()->with('success', 'Image(s) uploaded successfully.');
    }

    // Delete an image
    public function destroy(PatientImage $patientImage)
    {
        Storage::disk('public')->delete($patientImage->file_path);
        $patientImage->delete();

        return back()->with('success', 'Image removed.');
    }
}
