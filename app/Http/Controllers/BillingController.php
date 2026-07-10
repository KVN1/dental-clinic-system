<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        // Patients with outstanding balances, highest first
        $outstandingPatients = Patient::where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->get();

        $totalOutstanding = Patient::sum('balance');

        // Revenue totals
        $todayRevenue = PatientLog::whereDate('visit_date', today())->sum('amount_paid');
        $monthRevenue = PatientLog::whereMonth('visit_date', now()->month)
            ->whereYear('visit_date', now()->year)
            ->sum('amount_paid');
        $totalRevenue = PatientLog::sum('amount_paid');
        $totalCharged = PatientLog::sum('amount_charged');

        // Last 6 months revenue trend
        $monthlyTrend = PatientLog::select(
                DB::raw("strftime('%Y-%m', visit_date) as month"),
                DB::raw('SUM(amount_paid) as total')
            )
            ->where('visit_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('billing.index', compact(
            'outstandingPatients',
            'totalOutstanding',
            'todayRevenue',
            'monthRevenue',
            'totalRevenue',
            'totalCharged',
            'monthlyTrend'
        ));
    }
}