<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\User;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function index()
    {
        $users       = User::orderBy('name')->get();
        $patients    = Patient::orderBy('last_name')->get();
        $appSettings = AppSetting::current();
        $backups     = collect(File::exists(storage_path('app/backups')) ? File::files(storage_path('app/backups')) : [])
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 1),
                'date' => date('M d, Y g:i A', $file->getMTime()),
            ]);

        return view('settings.index', compact('users', 'patients', 'appSettings', 'backups'));
    }

    // ── Clinic Settings ────────────────────────────────────────────────────────

    public function updateClinic(Request $request)
    {
        $section  = $request->input('section', 'identity');
        $settings = AppSetting::current();
        $data     = [];

        switch ($section) {

            case 'identity':
                $data = $request->validate([
                    'clinic_name' => 'nullable|string|max:255',
                    'tagline'     => 'nullable|string|max:255',
                ]);
                if ($request->hasFile('logo')) {
                    $request->validate(['logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
                    if ($settings->logo) Storage::disk('public')->delete($settings->logo);
                    $data['logo'] = $request->file('logo')->store('clinic', 'public');
                }
                break;

            case 'contact':
                $data = $request->validate([
                    'address' => 'nullable|string|max:500',
                    'phone'   => 'nullable|string|max:50',
                    'email'   => 'nullable|email|max:255',
                    'website' => 'nullable|string|max:255',
                    'tin'     => 'nullable|string|max:50',
                ]);
                break;

            case 'billing':
                $data = $request->validate([
                    'currency_symbol'     => 'nullable|string|max:10',
                    'currency_code'       => 'nullable|string|max:10',
                    'default_tax_rate'    => 'nullable|numeric|min:0|max:100',
                    'receipt_footer_note' => 'nullable|string|max:500',
                    'payment_methods'     => 'nullable|array',
                ]);
                $data['show_tax_on_receipt'] = $request->boolean('show_tax_on_receipt');
                $data['payment_methods']     = $request->input('payment_methods', ['Cash']);
                break;

            case 'appearance':
                $data = $request->validate([
                    'date_format'      => 'nullable|string|max:30',
                    'timezone'         => 'nullable|string|max:50',
                    'primary_color'    => 'nullable|string|max:20',
                    'secondary_color'  => 'nullable|string|max:20',
                    'bg_color'         => 'nullable|string|max:20',
                    'surface_color'    => 'nullable|string|max:20',
                    'theme_preset'     => 'nullable|string|max:30',
                ]);
                break;
        }

        $settings->update($data);

        return back()->with('success', ucfirst($section) . ' settings saved successfully.');
    }

    public function removeLogo()
    {
        $settings = AppSetting::current();
        if ($settings->logo) {
            Storage::disk('public')->delete($settings->logo);
            $settings->update(['logo' => null]);
        }
        return back()->with('success', 'Logo removed.');
    }

    // ── Theme ──────────────────────────────────────────────────────────────────

    public function toggleTheme(Request $request)
    {
        $user = auth()->user();
        $user->theme = $user->theme === 'dark' ? 'light' : 'dark';
        $user->save();
        return back();
    }

    // ── Staff ──────────────────────────────────────────────────────────────────

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'role'      => 'required|in:admin,staff,dentist',
            'specialty' => 'nullable|string|max:255',
            'color'     => 'nullable|string|max:20',
        ]);

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => $validated['role'],
            'specialty' => $validated['specialty'] ?? null,
            'color'     => $validated['color'] ?? '#2A9D8F',
            'is_active' => true,
        ]);

        return back()->with('success', 'Account created successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }
        $user->delete();
        return back()->with('success', 'Account removed.');
    }

    // ── Exports ────────────────────────────────────────────────────────────────

    public function exportPatients(Request $request): StreamedResponse
    {
        $query = Patient::query();
        if ($request->filled('balance_filter')) {
            if ($request->balance_filter === 'with_balance') $query->where('balance', '>', 0);
            elseif ($request->balance_filter === 'paid_up')  $query->where('balance', '<=', 0);
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

    public function exportLogs(Request $request): StreamedResponse
    {
        $query = PatientLog::with('patient')->orderBy('visit_date');
        if ($request->filled('patient_id')) $query->where('patient_id', $request->patient_id);
        if ($request->filled('date_from'))  $query->whereDate('visit_date', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('visit_date', '<=', $request->date_to);
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

    public function exportPatientLogs(Request $request, Patient $patient): StreamedResponse
    {
        $query = $patient->logs();
        if ($request->filled('date_from')) $query->whereDate('visit_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('visit_date', '<=', $request->date_to);
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

    // ── Backups ────────────────────────────────────────────────────────────────

    public function createBackup()
    {
        $settings  = AppSetting::current();
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) File::makeDirectory($backupDir, 0755, true);

        $source   = database_path('database.sqlite');
        $filename = 'backup_' . date('Y-m-d_His') . '.sqlite';
        File::copy($source, $backupDir . '/' . $filename);

        if ($settings->backup_external_path && File::exists($settings->backup_external_path)) {
            File::copy($source, rtrim($settings->backup_external_path, '\\/') . '/' . $filename);
        }

        $settings->last_backup_at = now();
        $settings->save();

        return back()->with('success', 'Backup created: ' . $filename);
    }

    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (!File::exists($path)) abort(404);
        return response()->download($path);
    }

    public function updateBackupSettings(Request $request)
    {
        $validated = $request->validate([
            'backup_frequency_type' => 'required|in:daily,hourly',
            'backup_time'           => 'required|date_format:H:i',
            'backup_external_path'  => 'nullable|string',
        ]);

        AppSetting::current()->update($validated);
        return back()->with('success', 'Backup preferences updated.');
    }
}
