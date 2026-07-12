<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Appointment Summary Report</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:13px; color:#222; background:#f8faff; }
.page { max-width:850px; margin:0 auto; padding:28px 24px; }

.no-print { margin-bottom:16px; }
.btn { padding:8px 20px; border:none; border-radius:5px; cursor:pointer; font-size:13px; color:white; }
.btn-print { background:#1e4a8a; }
.btn-close { background:#888; margin-left:8px; }

.header { background:#1e4a8a; color:white; padding:18px 24px; border-radius:8px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
.clinic-name { font-size:20px; font-weight:bold; }
.clinic-sub { font-size:12px; opacity:0.8; }
.report-title { text-align:right; font-size:18px; font-weight:bold; }
.report-period { font-size:12px; opacity:0.8; }

.filter-bar { background:white; border:1px solid #dce6f7; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.filter-bar input, .filter-bar button { padding:7px 12px; border:1px solid #cdd; border-radius:5px; font-size:13px; }
.filter-bar button { background:#1e4a8a; color:white; border:none; cursor:pointer; }

.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
.stat-card { background:white; border:1px solid #dce6f7; border-radius:8px; padding:14px; text-align:center; }
.stat-num { font-size:20px; font-weight:bold; }
.stat-label { font-size:10px; color:#888; text-transform:uppercase; margin-top:3px; }
.stat-card.completed .stat-num { color:#16a34a; }
.stat-card.cancelled .stat-num { color:#dc2626; }
.stat-card.noshow .stat-num { color:#d97706; }
.stat-card.scheduled .stat-num { color:#1e4a8a; }

.day-section { background:white; border:1px solid #dce6f7; border-radius:8px; padding:16px; margin-bottom:14px; }
.day-title { font-size:13px; font-weight:bold; color:#1e4a8a; margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid #eef2ff; }

table { width:100%; border-collapse:collapse; font-size:12px; }
th { background:#eef2ff; color:#1e4a8a; padding:7px 10px; text-align:left; font-size:11px; text-transform:uppercase; }
td { padding:8px 10px; border-bottom:1px solid #f0f0f0; }

.status { display:inline-block; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:bold; }
.status-scheduled { background:#dbeafe; color:#1d4ed8; }
.status-confirmed { background:#e0f2fe; color:#0369a1; }
.status-completed { background:#dcfce7; color:#15803d; }
.status-no_show { background:#fef3c7; color:#92400e; }
.status-cancelled { background:#fee2e2; color:#991b1b; }

@media print {
    .no-print, .filter-bar { display:none; }
    body { background:white; }
}
</style>
</head>
<body>
<div class="page">

    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Print Report</button>
        <button class="btn btn-close" onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <div>
            <div class="clinic-name">{{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }}</div>
            <div class="clinic-sub">{{ $clinic->address ?? '' }}</div>
        </div>
        <div>
            <div class="report-title">Appointment Summary</div>
            <div class="report-period">{{ \Carbon\Carbon::parse($from)->format('M d') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.appointments') }}" class="filter-bar no-print">
        <label>From:</label>
        <input type="date" name="from" value="{{ $from }}">
        <label>To:</label>
        <input type="date" name="to" value="{{ $to }}">
        <button type="submit">View</button>
    </form>

    <div class="stats-grid">
        <div class="stat-card scheduled">
            <div class="stat-num">{{ $scheduled }}</div>
            <div class="stat-label">Scheduled</div>
        </div>
        <div class="stat-card completed">
            <div class="stat-num">{{ $completed }}</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card noshow">
            <div class="stat-num">{{ $noShow }}</div>
            <div class="stat-label">No Show</div>
        </div>
        <div class="stat-card cancelled">
            <div class="stat-num">{{ $cancelled }}</div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>

    @forelse($grouped as $date => $dayAppts)
    <div class="day-section">
        <div class="day-title">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }} ({{ $dayAppts->count() }} appointment{{ $dayAppts->count() != 1 ? 's' : '' }})</div>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Purpose</th>
                    <th>Notes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dayAppts as $appt)
                <tr>
                    <td>{{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $appt->patient->last_name ?? '' }}, {{ $appt->patient->first_name ?? '' }}</td>
                    <td>{{ $appt->purpose ?: '-' }}</td>
                    <td>{{ $appt->notes ?: '-' }}</td>
                    <td><span class="status status-{{ $appt->status }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @empty
    <div style="background:white;border:1px solid #dce6f7;border-radius:8px;padding:30px;text-align:center;color:#aaa;">
        No appointments found for this date range.
    </div>
    @endforelse

</div>
</body>
</html>
