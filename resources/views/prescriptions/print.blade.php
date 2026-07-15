<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Prescription - {{ $prescription->patient->first_name }} {{ $prescription->patient->last_name }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Georgia', serif; font-size: 13px; color: #222; background: #fff; }
.page { max-width: 620px; margin: 0 auto; padding: 36px 30px; }

.no-print { text-align: right; margin-bottom: 20px; }
.btn { padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; color: white; }
.btn-print { background: #1e4a8a; font-family: Arial, sans-serif; }
.btn-back { background: #888; margin-left: 8px; text-decoration: none; display: inline-block; font-family: Arial, sans-serif; }

.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px double #333; padding-bottom: 16px; margin-bottom: 20px; }
.clinic-name { font-size: 20px; font-weight: bold; color: #1e4a8a; font-family: Arial, sans-serif; }
.clinic-sub { font-size: 11px; color: #666; margin-top: 2px; font-family: Arial, sans-serif; }
.rx-symbol { font-size: 42px; font-weight: bold; color: #1e4a8a; font-family: Georgia, serif; line-height: 1; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; margin-bottom: 22px; font-family: Arial, sans-serif; }
.info-label { font-size: 9px; text-transform: uppercase; color: #999; letter-spacing: 0.5px; }
.info-value { font-size: 13px; font-weight: 600; color: #222; }

.meds-list { margin-bottom: 24px; }
.med-item { padding: 14px 0; border-bottom: 1px dashed #ddd; }
.med-item:last-child { border-bottom: none; }
.med-name { font-size: 15px; font-weight: bold; color: #1e4a8a; margin-bottom: 4px; }
.med-details { font-size: 12px; color: #444; font-family: Arial, sans-serif; line-height: 1.6; }
.med-details strong { color: #222; }
.med-instructions { font-size: 12px; color: #666; font-style: italic; margin-top: 4px; font-family: Arial, sans-serif; }

.notes-box { background: #f8faff; border: 1px solid #dce6f7; border-radius: 6px; padding: 12px 16px; margin-bottom: 24px; font-family: Arial, sans-serif; font-size: 12px; color: #555; }

.signature-area { margin-top: 50px; display: flex; justify-content: flex-end; }
.signature-line { text-align: center; width: 220px; }
.sig-space { height: 50px; border-bottom: 1px solid #333; margin-bottom: 6px; }
.sig-name { font-size: 12px; font-weight: bold; font-family: Arial, sans-serif; }
.sig-label { font-size: 10px; color: #888; font-family: Arial, sans-serif; }

.footer { text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 12px; margin-top: 30px; font-family: Arial, sans-serif; }

@media print {
    .no-print { display: none; }
    body { background: white; }
}
</style>
</head>
<body>
<div class="page">

    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Print Prescription</button>
        <a href="{{ route('patients.show', $prescription->patient) }}" class="btn btn-back">&larr; Back to Patient</a>
    </div>

    <div class="header">
        <div>
            @if($clinic->logo)
                <img src="{{ asset('storage/' . $clinic->logo) }}" style="height:40px;margin-bottom:6px;">
            @endif
            <div class="clinic-name">{{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }}</div>
            <div class="clinic-sub">{{ $clinic->address ?? '' }}</div>
            <div class="clinic-sub">{{ $clinic->phone ?? '' }} &nbsp;|&nbsp; {{ $clinic->email ?? '' }}</div>
        </div>
        <div class="rx-symbol">℞</div>
    </div>

    <div class="info-grid">
        <div>
            <div class="info-label">Patient</div>
            <div class="info-value">{{ $prescription->patient->last_name }}, {{ $prescription->patient->first_name }} {{ $prescription->patient->middle_name }}</div>
        </div>
        <div>
            <div class="info-label">Date Issued</div>
            <div class="info-value">{{ $prescription->date_issued->format('F d, Y') }}</div>
        </div>
        <div>
            <div class="info-label">Age / Sex</div>
            <div class="info-value">
                {{ $prescription->patient->birthdate ? \Carbon\Carbon::parse($prescription->patient->birthdate)->age . ' yrs' : 'N/A' }}
                / {{ ucfirst($prescription->patient->sex ?? 'N/A') }}
            </div>
        </div>
        <div>
            <div class="info-label">Prescribing Dentist</div>
            <div class="info-value">{{ $prescription->dentist->name ?? 'Not specified' }}</div>
        </div>
    </div>

    <div class="meds-list">
        @foreach($prescription->items as $i => $item)
            <div class="med-item">
                <div class="med-name">{{ $i + 1 }}. {{ $item->medication_name }}{{ $item->dosage ? ' — ' . $item->dosage : '' }}</div>
                <div class="med-details">
                    @if($item->frequency)<strong>Frequency:</strong> {{ $item->frequency }} &nbsp;&nbsp;@endif
                    @if($item->duration)<strong>Duration:</strong> {{ $item->duration }} &nbsp;&nbsp;@endif
                    @if($item->quantity)<strong>Quantity:</strong> {{ $item->quantity }}@endif
                </div>
                @if($item->instructions)
                    <div class="med-instructions">{{ $item->instructions }}</div>
                @endif
            </div>
        @endforeach
    </div>

    @if($prescription->notes)
        <div class="notes-box">
            <strong>Additional Notes:</strong> {{ $prescription->notes }}
        </div>
    @endif

    <div class="signature-area">
        <div class="signature-line">
            <div class="sig-space"></div>
            <div class="sig-name">{{ $prescription->dentist->name ?? 'Attending Dentist' }}</div>
            <div class="sig-label">Signature over Printed Name</div>
        </div>
    </div>

    <div class="footer">
        <p>{{ $clinic->clinic_name ?? 'Clear Smile Dental Clinic' }} &nbsp;|&nbsp; This prescription is valid for dispensing at any licensed pharmacy.</p>
        <p style="margin-top:4px;">Printed: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

</div>
</body>
</html>
