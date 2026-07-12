<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Record - {{ $patient->first_name }} {{ $patient->last_name }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 12px; color: #222; background: #fff; }
.page { max-width: 750px; margin: 0 auto; padding: 28px 24px; }

.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #1e4a8a; padding-bottom: 12px; margin-bottom: 18px; }
.clinic-name { font-size: 18px; font-weight: bold; color: #1e4a8a; }
.clinic-sub { font-size: 11px; color: #666; }

.doc-title { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #333; text-align: right; }

.section { margin-bottom: 18px; }
.section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #1e4a8a; border-bottom: 1px solid #dce6f7; padding-bottom: 4px; margin-bottom: 10px; }

.info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px 16px; }
.info-row { display: flex; flex-direction: column; }
.info-label { font-size: 9px; text-transform: uppercase; color: #999; letter-spacing: 0.5px; }
.info-value { font-size: 12px; font-weight: 600; color: #222; }

table { width: 100%; border-collapse: collapse; font-size: 11px; }
th { background: #eef2ff; color: #1e4a8a; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; }
tr:nth-child(even) td { background: #fafbff; }
.text-right { text-align: right; }
.badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 10px; font-weight: bold; }
.badge-visit { background: #dbeafe; color: #1d4ed8; }
.badge-payment { background: #dcfce7; color: #15803d; }
.badge-note { background: #fef9c3; color: #854d0e; }

.summary-box { background: #f0f4ff; border: 1px solid #dce6f7; border-radius: 6px; padding: 12px 16px; display: flex; justify-content: space-around; margin-bottom: 16px; }
.summary-item { text-align: center; }
.summary-num { font-size: 18px; font-weight: bold; color: #1e4a8a; }
.summary-label { font-size: 10px; color: #666; text-transform: uppercase; }

.footer { text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; margin-top: 20px; }

@media print {
    .no-print { display: none; }
    body { background: white; }
}
</style>
</head>
<body>
<div class="page">

    <div class="no-print" style="text-align:right; margin-bottom:16px;">
        <button onclick="window.print()" style="background:#1e4a8a;color:white;border:none;padding:8px 20px;border-radius:5px;cursor:pointer;font-size:13px;">Print Record</button>
        <button onclick="window.close()" style="background:#888;color:white;border:none;padding:8px 16px;border-radius:5px;cursor:pointer;font-size:13px;margin-left:8px;">Close</button>
    </div>

    <div class="header">
        <div>
            <div class="clinic-name">{{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }}</div>
            <div class="clinic-sub">{{ $clinic->address ?? '' }}</div>
            <div class="clinic-sub">{{ $clinic->phone ?? '' }} | {{ $clinic->email ?? '' }}</div>
        </div>
        <div>
            <div class="doc-title">Patient Record</div>
            <div style="font-size:10px;color:#aaa;text-align:right;">Printed: {{ now()->format('M d, Y') }}</div>
        </div>
    </div>

    {{-- Patient Info --}}
    <div class="section">
        <div class="section-title">Patient Information</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Full Name</span>
                <span class="info-value">{{ $patient->last_name }}, {{ $patient->first_name }} {{ $patient->middle_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Birthdate</span>
                <span class="info-value">{{ $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sex</span>
                <span class="info-value">{{ ucfirst($patient->sex ?? 'N/A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Contact</span>
                <span class="info-value">{{ $patient->contact_number ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $patient->email ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Blood Type</span>
                <span class="info-value">{{ $patient->blood_type ?? 'N/A' }}</span>
            </div>
            <div class="info-row" style="grid-column: span 3">
                <span class="info-label">Address</span>
                <span class="info-value">{{ $patient->address ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    {{-- Medical Info --}}
    <div class="section">
        <div class="section-title">Medical History</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Allergies</span>
                <span class="info-value">{{ $patient->allergies ?? 'None reported' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Medications</span>
                <span class="info-value">{{ $patient->medications ?? 'None' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Blood Pressure</span>
                <span class="info-value">{{ $patient->blood_pressure ?? 'N/A' }}</span>
            </div>
            <div class="info-row" style="grid-column: span 3">
                <span class="info-label">Medical Conditions</span>
                <span class="info-value">{{ $patient->medical_conditions ?? 'None reported' }}</span>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-num">{{ $logs->count() }}</div>
            <div class="summary-label">Total Visits</div>
        </div>
        <div class="summary-item">
            <div class="summary-num">₱{{ number_format($logs->sum('amount_charged'), 2) }}</div>
            <div class="summary-label">Total Charged</div>
        </div>
        <div class="summary-item">
            <div class="summary-num">₱{{ number_format($logs->sum('amount_paid'), 2) }}</div>
            <div class="summary-label">Total Paid</div>
        </div>
        <div class="summary-item">
            <div class="summary-num" style="color: {{ $patient->balance > 0 ? '#dc2626' : '#16a34a' }}">
                ₱{{ number_format($patient->balance, 2) }}
            </div>
            <div class="summary-label">Outstanding Balance</div>
        </div>
    </div>

    {{-- Visit History --}}
    <div class="section">
        <div class="section-title">Visit & Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Tooth</th>
                    <th>Description</th>
                    <th class="text-right">Charged</th>
                    <th class="text-right">Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->visit_date)->format('M d, Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $log->entry_type }}">{{ ucfirst($log->entry_type) }}</span>
                    </td>
                    <td>{{ $log->tooth_number ?: '-' }}</td>
                    <td>{{ $log->description ?: '-' }}</td>
                    <td class="text-right">₱{{ number_format($log->amount_charged, 2) }}</td>
                    <td class="text-right">₱{{ number_format($log->amount_paid, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#aaa;padding:16px;">No records found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>{{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }} &nbsp;|&nbsp; Patient Record &nbsp;|&nbsp; Confidential</p>
        <p>This document is for official use only. Unauthorized disclosure is prohibited.</p>
    </div>

</div>
</body>
</html>
