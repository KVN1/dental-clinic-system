<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    // Returns JSON of appointments for a given month, grouped by day
    public function month(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);
        $dentistId = $request->input('dentist_id');

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $query = Appointment::with(['patient', 'dentist'])
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotIn('status', ['cancelled']);

        if ($dentistId) {
            $query->where('dentist_id', $dentistId);
        }

        $appointments = $query->orderBy('appointment_time')->get();

        $byDay = $appointments->groupBy(fn ($a) => $a->appointment_date->format('Y-m-d'))
            ->map(function ($dayAppts) {
                return $dayAppts->map(function ($a) {
                    return [
                        'id'          => $a->id,
                        'time'        => $a->appointment_time ? Carbon::parse($a->appointment_time)->format('g:i A') : null,
                        'patient'     => $a->patient ? $a->patient->last_name . ', ' . $a->patient->first_name : 'Unknown',
                        'purpose'     => $a->purpose,
                        'status'      => $a->status,
                        'dentist'     => $a->dentist->name ?? null,
                        'dentist_color' => $a->dentist->color ?? '#2A9D8F',
                        'edit_url'    => route('appointments.edit', $a),
                    ];
                });
            });

        return response()->json([
            'month' => $month,
            'year'  => $year,
            'days'  => $byDay,
        ]);
    }
}