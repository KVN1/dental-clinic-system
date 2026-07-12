<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monthly Revenue Report</title>
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
.clinic-sub { font-size:12px; opacity:0.8; margin-top:2px; }
.report-title { text-align:right; font-size:18px; font-weight:bold; }
.report-period { font-size:12px; opacity:0.8; }

.filter-bar { background:white; border:1px solid #dce6f7; border-radius:8px; padding:14px 18px; margin-bottom:20px; display:flex; gap:12px; align-items:center; }
.filter-bar select, .filter-bar button { padding:7px 12px; border:1px solid #cdd; border-radius:5px; font-size:13px; }
.filter-bar button { background:#1e4a8a; color:white; border:none; cursor:pointer; }

.stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px; }
.stat-card { background:white; border:1px solid #dce6f7; border-radius:8px; padding:16px; text-align:center; }
.stat-num { font-size:22px; font-weight:bold; color:#1e4a8a; }
.stat-label { font-size:11px; color:#888; text-transform:uppercase; margin-top:4px; }
.stat-card.danger .stat-num { color:#dc2626; }
.stat-card.success .stat-num { color:#16a34a; }

.section { background:white; border:1px solid #dce6f7; border-radius:8px; padding:18px; margin-bottom:18px; }
.section-title { font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#1e4a8a; border-bottom:1px solid #eef2ff; padding-bottom:8px; margin-bottom:14px; }

table { width:100%; border-collapse:collapse; font-size:12px; }
th { background:#eef2ff; color:#1e4a8a; padding:8px 10px; text-align:left; font-size:11px; text-transform:uppercase; }
td { padding:8px 10px; border-bottom:1px solid #f0f0f0; }
tr:hover td { background:#fafbff; }
.text-right { text-align:right; }
.badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:bold; }
.badge-visit { background:#dbeafe; color:#1d4ed8; }
.badge-payment { background:#dcfce7; color:#15803d; }
.badge-note { background:#fef9c3; color:#854d0e; }

@media print {
    .no-print, .filter-bar { display:none; }
    body { background:white; }
    .page { padding:10px; }
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
            <div class="report-title">Monthly Revenue Report</div>
            <div class="report-period">{{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('reports.monthly') }}" class="filter-bar no-print">
        <label>Month:</label>
        <select name="month">
            @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ $num == $month ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        <label>Year:</label>
        <select name="year">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit">View</button>
    </form>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-num">{{ $logs->count() }}</div>
            <div class="stat-label">Total Entries</div>
        </div>
        <div class="stat-card success">
            <div class="stat-num">₱{{ number_format($totalPaid, 2) }}</div>
            <div class="stat-label">Total Collected</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-num">₱{{ number_format($totalBalance, 2) }}</div>
            <div class="stat-label">Uncollected</div>
        </div>
    </div>

    {{-- Procedure Summary --}}
    @if($procedures->count())
    <div class="section">
        <div class="section-title">Procedure Summary</div>
        <table>
            <thead>
                <tr>
                    <th>Procedure</th>
                    <th class="text-right">Count</th>
                    <th class="text-right">Total Charged</th>
                    <th class="text-right">Total Paid</th>
                </tr>
            </thead>
            <tbody>
                @foreach($procedures as $name => $data)
                <tr>
                    <td>{{ $name }}</td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">₱{{ number_format($data['charged'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($data['paid'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- All Entries --}}
    <div class="section">
        <div class="section-title">All Entries for {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th class="text-right">Charged</th>
                    <th class="text-right">Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->visit_date)->format('M d') }}</td>
                    <td>{{ $log->patient->last_name ?? '' }}, {{ $log->patient->first_name ?? '' }}</td>
                    <td><span class="badge badge-{{ $log->entry_type }}">{{ ucfirst($log->entry_type) }}</span></td>
                    <td>{{ $log->description ?: '-' }}</td>
                    <td class="text-right">₱{{ number_format($log->amount_charged, 2) }}</td>
                    <td class="text-right">₱{{ number_format($log->amount_paid, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">No entries for this month</td></tr>
                @endforelse
            </tbody>
            @if($logs->count())
            <tfoot>
                <tr style="background:#eef2ff;font-weight:bold;">
                    <td colspan="4" style="text-align:right;">TOTAL</td>
                    <td class="text-right">₱{{ number_format($totalCharged, 2) }}</td>
                    <td class="text-right">₱{{ number_format($totalPaid, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>
</body>
</html>
