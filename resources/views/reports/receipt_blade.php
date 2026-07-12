<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Receipt - {{ $patient->first_name }} {{ $patient->last_name }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 13px; color: #222; background: #fff; }
.page { max-width: 600px; margin: 0 auto; padding: 30px 24px; }

.header { text-align: center; border-bottom: 2px solid #1e4a8a; padding-bottom: 14px; margin-bottom: 18px; }
.clinic-name { font-size: 20px; font-weight: bold; color: #1e4a8a; }
.clinic-sub { font-size: 12px; color: #555; margin-top: 3px; }

.receipt-title { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 18px; color: #333; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; margin-bottom: 16px; }
.info-row { display: flex; flex-direction: column; }
.info-label { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
.info-value { font-size: 13px; font-weight: 600; color: #222; }

.divider { border: none; border-top: 1px dashed #ccc; margin: 14px 0; }

table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
th { background: #f0f4ff; color: #1e4a8a; font-size: 11px; text-transform: uppercase; padding: 7px 10px; text-align: left; }
td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
.text-right { text-align: right; }

.totals { background: #f8faff; border: 1px solid #dce6f7; border-radius: 6px; padding: 14px 16px; margin-bottom: 20px; }
.total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
.total-row.balance { font-weight: bold; font-size: 15px; border-top: 1px solid #dce6f7; margin-top: 6px; padding-top: 8px; color: #1e4a8a; }

.footer { text-align: center; font-size: 11px; color: #888; border-top: 1px solid #eee; padding-top: 14px; }
.receipt-no { font-size: 11px; color: #aaa; text-align: right; margin-bottom: 6px; }

@media print {
    body { background: white; }
    .no-print { display: none; }
    .page { padding: 10px; }
}
</style>
</head>
<body>
<div class="page">

    <div class="no-print" style="text-align:right; margin-bottom:16px;">
        <button onclick="window.print()" style="background:#1e4a8a;color:white;border:none;padding:8px 20px;border-radius:5px;cursor:pointer;font-size:13px;">Print Receipt</button>
        <button onclick="window.close()" style="background:#888;color:white;border:none;padding:8px 16px;border-radius:5px;cursor:pointer;font-size:13px;margin-left:8px;">Close</button>
    </div>

    <div class="header">
        @if($clinic && $clinic->logo)
            <img src="{{ asset('storage/' . $clinic->logo) }}" style="height:50px;margin-bottom:6px;"><br>
        @endif
        <div class="clinic-name">{{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }}</div>
        <div class="clinic-sub">{{ $clinic->address ?? '' }}</div>
        <div class="clinic-sub">{{ $clinic->phone ?? '' }} &nbsp;|&nbsp; {{ $clinic->email ?? '' }}</div>
    </div>

    <div class="receipt-title">Official Receipt</div>

    <div class="receipt-no">Receipt #{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</div>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Patient Name</span>
            <span class="info-value">{{ $patient->last_name }}, {{ $patient->first_name }} {{ $patient->middle_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($log->visit_date)->format('F d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contact</span>
            <span class="info-value">{{ $patient->contact_number ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Recorded By</span>
            <span class="info-value">{{ $log->recorded_by ?? 'Staff' }}</span>
        </div>
    </div>

    <hr class="divider">

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Tooth</th>
                <th class="text-right">Charged</th>
                <th class="text-right">Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $log->description ?: 'Dental Service' }}</td>
                <td>{{ $log->tooth_number ?: '-' }}</td>
                <td class="text-right">₱{{ number_format($log->amount_charged, 2) }}</td>
                <td class="text-right">₱{{ number_format($log->amount_paid, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Amount Charged</span>
            <span>₱{{ number_format($log->amount_charged, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Amount Paid</span>
            <span>₱{{ number_format($log->amount_paid, 2) }}</span>
        </div>
        <div class="total-row balance">
            <span>Outstanding Balance</span>
            <span>₱{{ number_format($patient->balance, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for trusting {{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }}.</p>
        <p style="margin-top:4px;">This serves as your official receipt. Please keep for your records.</p>
        <p style="margin-top:8px;color:#bbb;">Printed: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

</div>
</body>
</html>
