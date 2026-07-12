<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\Appointment;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Monthly Revenue Report
    public function monthlyRevenue(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $logs = PatientLog::with('patient')
            ->whereMonth('visit_date', $month)
            ->whereYear('visit_date', $year)
            ->orderBy('visit_date')
            ->get();

        $totalCharged  = $logs->sum('amount_charged');
        $totalPaid     = $logs->sum('amount_paid');
        $totalBalance  = $totalCharged - $totalPaid;

        // Daily breakdown
        $dailyBreakdown = $logs->groupBy(fn($l) => $l->visit_date);

        // Procedure summary
        $procedures = $logs->where('entry_type', 'visit')
            ->whereNotNull('description')
            ->groupBy('description')
            ->map(fn($group) => [
                'count'   => $group->count(),
                'charged' => $group->sum('amount_charged'),
                'paid'    => $group->sum('amount_paid'),
            ]);

        $months = collect(range(1, 12))->mapWithKeys(fn($m) => [
            $m => \Carbon\Carbon::create()->month($m)->format('F')
        ]);

        $years = range(now()->year - 2, now()->year);

        $clinic = AppSetting::first();

        return view('reports.monthly', compact(
            'logs', 'totalCharged', 'totalPaid', 'totalBalance',
            'dailyBreakdown', 'procedures', 'month', 'year',
            'months', 'years', 'clinic'
        ));
    }

    // Appointment Summary Report
    public function appointmentSummary(Request $request)
    {
        $from = $request->get('from', now()->startOfWeek()->toDateString());
        $to   = $request->get('to',   now()->endOfWeek()->toDateString());

        $appointments = Appointment::with('patient')
            ->whereBetween('appointment_date', [$from, $to])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $grouped   = $appointments->groupBy(fn($a) => $a->appointment_date->format('Y-m-d'));
        $completed = $appointments->where('status', 'completed')->count();
        $cancelled = $appointments->where('status', 'cancelled')->count();
        $noShow    = $appointments->where('status', 'no_show')->count();
        $scheduled = $appointments->whereIn('status', ['scheduled', 'confirmed'])->count();

        $clinic = AppSetting::first();

        return view('reports.appointments', compact(
            'appointments', 'grouped', 'from', 'to',
            'completed', 'cancelled', 'noShow', 'scheduled', 'clinic'
        ));
    }

    // Print Receipt for a patient visit log
    public function printReceipt(Request $request, Patient $patient)
    {
        $logId = $request->get('log_id');
        $log   = $logId
            ? $patient->logs()->findOrFail($logId)
            : $patient->logs()->latest('visit_date')->first();

        $clinic = AppSetting::first();

        return view('reports.receipt', compact('patient', 'log', 'clinic'));
    }

    // Print full patient record
    public function printPatientRecord(Patient $patient)
    {
        $logs  = $patient->logs;
        $clinic = AppSetting::first();

        return view('reports.patient-record', compact('patient', 'logs', 'clinic'));
    }
}
