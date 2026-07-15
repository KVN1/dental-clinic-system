<x-clinic-layout :title="$patient->first_name . ' ' . $patient->last_name">

<div class="profile-header-row">
        <a href="{{ route('patients.index') }}" class="back-link">← Back to Patients</a>
        <a href="{{ route('patients.receipt', $patient) }}" target="_blank">Print Receipt</a>
<a href="{{ route('patients.print', $patient) }}" target="_blank">Print Record</a>

        <div class="profile-header-actions">
            <a href="{{ route('patients.edit', $patient) }}" class="btn-secondary">✎ Edit Patient</a>

            <form method="GET" action="{{ route('patients.export', $patient) }}" class="export-inline-form">
                <input type="date" name="date_from" title="From date">
                <input type="date" name="date_to" title="To date">
                <button type="submit" class="btn-secondary">⬇ Export</button>
            </form>

            <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('Permanently delete this patient and all their visit history? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">🗑 Delete</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="status-message" style="margin-bottom: 1.25rem;">{{ session('success') }}</div>
    @endif

    <!-- Merged Patient + Medical Info Card -->
    <div class="panel" style="margin-bottom: 1.5rem;">
        <div class="profile-card-header">
            <div class="profile-avatar">{{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}</div>
            <div>
                <h2 class="font-display profile-name">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
                @if ($patient->nickname)
                    <div class="profile-nickname">"{{ $patient->nickname }}"</div>
                @endif
            </div>
            <div class="profile-balance-inline {{ $patient->balance > 0 ? 'balance-due-text' : 'balance-clear-text' }}">
                @if ($patient->balance > 0)
                    Balance Due: ₱{{ number_format($patient->balance, 2) }}
                @else
                    Paid up
                @endif
            </div>
        </div>

        <div class="info-columns">
            <div class="info-column">
                <h4 class="info-column-title">Personal</h4>
                <div class="profile-detail-grid">
                    <div class="profile-detail">
                        <span class="detail-label">Birthdate</span>
                        <span>{{ $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('M d, Y') : '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Sex</span>
                        <span>{{ $patient->sex ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Occupation</span>
                        <span>{{ $patient->occupation ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Contact</span>
                        <span>{{ $patient->contact_number ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Email</span>
                        <span>{{ $patient->email ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Address</span>
                        <span>{{ $patient->address ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Emergency Contact</span>
                        <span>{{ $patient->emergency_contact_name ?? '—' }} @if($patient->emergency_contact_number) ({{ $patient->emergency_contact_number }}) @endif</span>
                    </div>
                </div>
            </div>

            <div class="info-column">
                <h4 class="info-column-title">Dental History</h4>
                <div class="profile-detail-grid">
                    <div class="profile-detail">
                        <span class="detail-label">Referred By</span>
                        <span>{{ $patient->referred_by ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Previous Dentist</span>
                        <span>{{ $patient->previous_dentist ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Last Dental Visit</span>
                        <span>{{ $patient->last_dental_visit ? \Carbon\Carbon::parse($patient->last_dental_visit)->format('M d, Y') : '—' }}</span>
                    </div>
                    <div class="profile-detail profile-detail-full">
                        <span class="detail-label">Reason for Consultation</span>
                        <span>{{ $patient->reason_for_consultation ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="info-column">
                <h4 class="info-column-title">Medical</h4>
                <div class="profile-detail-grid">
                    <div class="profile-detail">
                        <span class="detail-label">Allergies</span>
                        <span>{{ $patient->allergies ?? 'None reported' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Medications</span>
                        <span>{{ $patient->medications ?? 'None reported' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Physician</span>
                        <span>{{ $patient->physician_name ?? '—' }} @if($patient->physician_specialty) ({{ $patient->physician_specialty }}) @endif</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Blood Type</span>
                        <span>{{ $patient->blood_type ?? '—' }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="detail-label">Blood Pressure</span>
                        <span>{{ $patient->blood_pressure ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($patient->medical_conditions_checklist && count($patient->medical_conditions_checklist) > 0)
            <div class="condition-tags">
                @foreach ($patient->medical_conditions_checklist as $condition)
                    <span class="condition-tag">{{ $condition }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Add Entry + History, evenly matched -->
    <div class="profile-bottom-grid">

        <div class="panel profile-bottom-panel">
            <h3 class="form-section-title">Add Visit / Payment / Note</h3>

            <form method="POST" action="{{ route('patients.logs.store', $patient) }}" class="clinic-form entry-form" x-data="{ entryType: 'visit' }">
                @csrf

                <div class="entry-form-grid">
                    <div class="field-group">
                        <label for="visit_date">Date</label>
                        <input id="visit_date" type="date" name="visit_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="field-group">
                        <label for="entry_type">Type</label>
                        <select id="entry_type" name="entry_type" x-model="entryType" required>
                            <option value="visit">Visit / Procedure</option>
                            <option value="payment">Payment Only</option>
                            <option value="note">General Note</option>
                        </select>
                    </div>

                    <div class="field-group" x-show="entryType === 'visit'">
                        <label for="tooth_number">Tooth No./s</label>
                        <input id="tooth_number" type="text" name="tooth_number" value="{{ old('tooth_number') }}" placeholder="e.g. #14, #15">
                    </div>

                    <div class="field-group" x-show="entryType === 'visit'" x-cloak>
                        <label for="log_dentist_id">Dentist</label>
                        <select id="log_dentist_id" name="dentist_id">
                            <option value="">— Not specified —</option>
                            @foreach($dentists ?? [] as $d)
                                <option value="{{ $d->id }}" {{ auth()->user()->isDentist() && auth()->id() === $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field-group field-group-full">
                        <label for="description">Description</label>
                        <input
                            id="description"
                            name="description"
                            list="procedure-options"
                            placeholder="e.g. Tooth extraction, upper left molar"
                            autocomplete="off"
                        >
                        <span class="settings-sublabel">Leave blank if only settling balance.</span>
                        <datalist id="procedure-options">
                            <option value="General Checkup">
                            <option value="Cleaning / Prophylaxis">
                            <option value="Tooth Extraction (Removal)">
                            <option value="Filling / Pasta">
                            <option value="Root Canal Treatment">
                            <option value="Tooth Whitening">
                            <option value="Braces Adjustment">
                            <option value="Denture Fitting">
                            <option value="X-Ray">
                            <option value="Consultation">
                            <option value="Balance Payment">
                        </datalist>
                    </div>

                    <div class="field-group">
                        <label for="amount_charged">Amount Charged (₱)</label>
                        <input id="amount_charged" type="number" step="0.01" min="0" name="amount_charged" value="0">
                        <span class="field-warning" x-show="entryType === 'payment'" x-cloak>
                            ⚠ Leave at 0 for balance-only payments.
                        </span>
                    </div>

                    <div class="field-group">
                        <label for="amount_paid">Amount Paid (₱)</label>
                        <input id="amount_paid" type="number" step="0.01" min="0" name="amount_paid" value="0">
                    </div>

                    <div class="field-group" x-show="entryType === 'payment'" x-cloak>
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method">
                            @forelse($paymentMethods ?? ['Cash'] as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @empty
                                <option value="Cash">Cash</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="form-footer" style="justify-content: flex-start;">
                    <button type="submit" class="btn-primary">Save Entry</button>
                </div>
            </form>
        </div>

        <div class="panel profile-bottom-panel">
            <h3 class="form-section-title">Visit History</h3>

            <div class="timeline timeline-scrollable">
                @forelse ($logs as $log)
                    <div class="timeline-item">
                        <div class="timeline-marker timeline-{{ $log->entry_type }}"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-date">{{ \Carbon\Carbon::parse($log->visit_date)->format('M d, Y') }}</span>
                                @if ($log->tooth_number)
                                    <span class="tooth-tag">🦷 {{ $log->tooth_number }}</span>
                                @endif
                                <span class="timeline-type-tag timeline-type-{{ $log->entry_type }}">{{ ucfirst($log->entry_type) }}</span>
                            </div>
                            <p class="timeline-desc">{{ $log->description }}</p>
                            @if ($log->amount_charged > 0 || $log->amount_paid > 0)
                                <div class="timeline-amounts">
                                    @if ($log->amount_charged > 0)
                                        <span>Charged: ₱{{ number_format($log->amount_charged, 2) }}</span>
                                    @endif
                                    @if ($log->amount_paid > 0)
                                        <span>Paid: ₱{{ number_format($log->amount_paid, 2) }}</span>
                                        @if ($log->payment_method)
                                            <span style="color:var(--color-muted);">via {{ $log->payment_method }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endif
                            <div class="timeline-meta">
                                Logged by {{ $log->recorded_by }}
                                @if($log->dentist)
                                    &middot; Dentist: {{ $log->dentist->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-row">No visits logged yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</x-clinic-layout>