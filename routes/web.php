<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $totalPatients = \App\Models\Patient::count();
    $totalBalance = \App\Models\Patient::sum('balance');
    $recentPatients = \App\Models\Patient::orderBy('created_at', 'desc')->take(5)->get();

    $todaysAppointmentsList = \App\Models\Appointment::with(['patient', 'dentist'])
        ->whereDate('appointment_date', today())
        ->whereNotIn('status', ['cancelled'])
        ->orderBy('appointment_time')
        ->get();

    $todaysAppointments = $todaysAppointmentsList->count();

    $weekAppointments = \App\Models\Appointment::with(['patient', 'dentist'])
        ->whereBetween('appointment_date', [today()->addDay(), today()->addDays(7)])
        ->whereNotIn('status', ['cancelled'])
        ->orderBy('appointment_date')
        ->orderBy('appointment_time')
        ->get();

    $overdueBalances = \App\Models\Patient::where('balance', '>', 0)
        ->orderBy('balance', 'desc')
        ->take(8)
        ->get();

    $stalePrescriptions = \App\Models\Prescription::with('patient')
        ->where('status', 'active')
        ->where('date_issued', '<=', now()->subDays(30))
        ->orderBy('date_issued')
        ->take(5)
        ->get();

    $recentReschedules = \App\Models\AppointmentReschedule::with(['appointment.patient', 'appointment.dentist'])
        ->where('created_at', '>=', now()->subDays(7))
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('dashboard', compact(
        'totalPatients', 'totalBalance', 'recentPatients',
        'todaysAppointments', 'todaysAppointmentsList',
        'weekAppointments', 'overdueBalances', 'stalePrescriptions',
        'recentReschedules'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::post('/patients/{patient}/images', [App\Http\Controllers\PatientImageController::class, 'store'])->name('patients.images.store');
    Route::delete('/patient-images/{patientImage}', [App\Http\Controllers\PatientImageController::class, 'destroy'])->name('patients.images.destroy');

    Route::post('/patients/{patient}/prescriptions', [App\Http\Controllers\PrescriptionController::class, 'store'])->name('patients.prescriptions.store');
    Route::patch('/prescriptions/{prescription}/status', [App\Http\Controllers\PrescriptionController::class, 'updateStatus'])->name('prescriptions.status');
    Route::delete('/prescriptions/{prescription}', [App\Http\Controllers\PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
    Route::get('/prescriptions/{prescription}/print', [App\Http\Controllers\PrescriptionController::class, 'print'])->name('prescriptions.print');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/provider', [ProfileController::class, 'updateProviderInfo'])->name('profile.provider.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/provider', [ProfileController::class, 'updateProviderInfo'])->name('profile.provider.update');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::post('/patients/{patient}/logs', [PatientController::class, 'storeLog'])->name('patients.logs.store');
    Route::get('/patients/{patient}/export', [SettingsController::class, 'exportPatientLogs'])->name('patients.export');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

    Route::post('/privacy/reveal', [PrivacyController::class, 'reveal'])->name('privacy.reveal');
    Route::post('/privacy/hide', [PrivacyController::class, 'hide'])->name('privacy.hide');

    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
Route::get('/appointments/calendar-data', [App\Http\Controllers\CalendarController::class, 'month'])->name('appointments.calendar.data');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/theme', [SettingsController::class, 'toggleTheme'])->name('settings.theme');
    Route::get('/settings/export/patients', [SettingsController::class, 'exportPatients'])->name('settings.export.patients');
    Route::get('/settings/export/logs', [SettingsController::class, 'exportLogs'])->name('settings.export.logs');

    Route::middleware('admin')->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');

        // Reports
        Route::get('/reports/monthly', [ReportController::class, 'monthlyRevenue'])->name('reports.monthly');
        Route::get('/reports/appointments', [ReportController::class, 'appointmentSummary'])->name('reports.appointments');

        // Print receipt - specific log or latest
        Route::get('/patients/{patient}/receipt', [ReportController::class, 'printReceipt'])->name('patients.receipt');

        // Print full patient record
        Route::get('/patients/{patient}/print', [ReportController::class, 'printPatientRecord'])->name('patients.print');
        Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
        Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])->name('settings.users.destroy');
        Route::post('/settings/backup', [SettingsController::class, 'createBackup'])->name('settings.backup');
        Route::get('/settings/backup/{filename}', [SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
        Route::post('/settings/backup-preferences', [SettingsController::class, 'updateBackupSettings'])->name('settings.backup.preferences');

        // Clinic Settings
        Route::post('/settings/clinic', [SettingsController::class, 'updateClinic'])->name('settings.clinic');
        Route::get('/settings/clinic/remove-logo', [SettingsController::class, 'removeLogo'])->name('settings.clinic.remove-logo');
    });
});

require __DIR__.'/auth.php';
