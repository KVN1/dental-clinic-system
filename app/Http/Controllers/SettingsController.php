<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
public function index()
    {
        $users = User::orderBy('name')->get();
        $patients = Patient::orderBy('last_name')->get();
        $appSettings = \App\Models\AppSetting::current();
        $backups = collect(File::exists(storage_path('app/backups')) ? File::files(storage_path('app/backups')) : [])
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 1),
                'date' => date('M d, Y g:i A', $file->getMTime()),
            ]);

        return view('settings.index', compact('users', 'patients', 'appSettings', 'backups'));
    }

    // Toggle dark/light mode
    public function toggleTheme(Request $request)
    {
        $user = auth()->user();
        $user->theme = $user->theme === 'dark' ? 'light' : 'dark';
        $user->save();

        return back();
    }

    // Add a new staff account
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,staff',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return back()->with('success', 'Account created successfully.');
    }

    // Remove a staff account
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', 'Account removed.');
    }

// Export patients as CSV (optionally filtered by balance status)
    public function exportPatients(Request $request): StreamedResponse
    {
        $query = Patient::query();

        if ($request->filled('balance_filter')) {
            if ($request->balance_filter === 'with_balance') {
                $query->where('balance', '>', 0);
            } elseif ($request->balance_filter === 'paid_up') {
                $query->where('balance', '<=', 0);
            }
        }

        $patients = $query->orderBy('last_name')->get();

        return response()->streamDownload(function () use ($patients) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['First Name', 'Last Name', 'Birthdate', 'Sex', 'Contact', 'Email', 'Balance']);
            foreach ($patients as $p) {
                fputcsv($handle, [$p->first_name, $p->last_name, $p->birthdate, $p->sex, $p->contact_number, $p->email, $p->balance]);
            }
            fclose($handle);
        }, 'patients_' . date('Y-m-d') . '.csv');
    }

    // Export logs as CSV (optionally filtered by patient and/or date range)
    public function exportLogs(Request $request): StreamedResponse
    {
        $query = PatientLog::with('patient')->orderBy('visit_date');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }

        $logs = $query->get();

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Patient', 'Type', 'Description', 'Charged', 'Paid', 'Recorded By']);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->visit_date,
                    $log->patient->last_name . ', ' . $log->patient->first_name,
                    $log->entry_type,
                    $log->description,
                    $log->amount_charged,
                    $log->amount_paid,
                    $log->recorded_by,
                ]);
            }
            fclose($handle);
        }, 'logs_' . date('Y-m-d') . '.csv');
    }

// Export a single patient's logs as CSV (optionally filtered by date range)
    public function exportPatientLogs(Request $request, Patient $patient): StreamedResponse
    {
        $query = $patient->logs();

        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }

        $logs = $query->get();

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Type', 'Description', 'Charged', 'Paid', 'Recorded By']);
            foreach ($logs as $log) {
                fputcsv($handle, [$log->visit_date, $log->entry_type, $log->description, $log->amount_charged, $log->amount_paid, $log->recorded_by]);
            }
            fclose($handle);
        }, str_replace(' ', '_', $patient->first_name . '_' . $patient->last_name) . '_logs_' . date('Y-m-d') . '.csv');
    }

    // Create a full database backup (raw SQLite file copy)
// Create a full database backup (raw SQLite file copy)
    public function createBackup()
    {
        $settings = \App\Models\AppSetting::current();

        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $source = database_path('database.sqlite');
        $filename = 'backup_' . date('Y-m-d_His') . '.sqlite';
        File::copy($source, $backupDir . '/' . $filename);

        // Also copy to external folder, if one is configured and exists
        if ($settings->backup_external_path && File::exists($settings->backup_external_path)) {
            File::copy($source, rtrim($settings->backup_external_path, '\\/') . '/' . $filename);
        }

        $settings->last_backup_at = now();
        $settings->save();

        return back()->with('success', 'Backup created: ' . $filename);
    }
    // Download a specific backup file
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (! File::exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

public function updateBackupSettings(Request $request)
    {
        $validated = $request->validate([
            'backup_frequency_type' => 'required|in:daily,hourly',
            'backup_time' => 'required|date_format:H:i',
            'backup_external_path' => 'nullable|string',
        ]);

        $settings = \App\Models\AppSetting::current();
        $settings->update($validated);

        return back()->with('success', 'Backup preferences updated.');
    }
}