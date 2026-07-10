<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
$query = PatientLog::with('patient')->orderBy('visit_date', 'desc')->orderBy('created_at', 'desc');
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('visit_date', '<=', $request->date_to);
        }

        $logs = $query->paginate(10)->withQueryString();

        $patients = Patient::orderBy('last_name')->get();

        return view('logs.index', compact('logs', 'patients'));
    }
}