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

<div class="panel profile-bottom-panel" style="margin-top:1.5rem;" x-data="{ showRxForm: false, rxItems: [{}] }">

    <div class="panel-header">
        <h2 class="panel-title">Prescriptions</h2>
        <button type="button" class="btn-primary" style="font-size:12px;padding:6px 14px;" @click="showRxForm = !showRxForm">
            <span x-text="showRxForm ? 'Cancel' : '+ New Prescription'"></span>
        </button>
    </div>

    <!-- New Prescription Form -->
    <div x-show="showRxForm" x-cloak style="margin-bottom:1.5rem;padding:16px;background:var(--color-bg);border-radius:10px;">
        <form method="POST" action="{{ route('patients.prescriptions.store', $patient) }}" class="clinic-form">
            @csrf

            <div class="form-grid" style="margin-bottom:12px;">
                <div class="field-group">
                    <label for="rx_date_issued">Date Issued</label>
                    <input id="rx_date_issued" type="date" name="date_issued" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="field-group">
                    <label for="rx_dentist_id">Prescribing Dentist</label>
                    <select id="rx_dentist_id" name="dentist_id">
                        <option value="">— Not specified —</option>
                        @foreach($dentists ?? [] as $d)
                            <option value="{{ $d->id }}" {{ auth()->user()->isDentist() && auth()->id() === $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label style="display:block;margin-bottom:8px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);">Medications</label>

            <template x-for="(item, index) in rxItems" :key="index">
                <div style="border:1px solid var(--color-border, #E7ECEB);border-radius:8px;padding:12px;margin-bottom:10px;background:var(--color-surface);">
                    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:8px;">
                        <div class="field-group" style="margin:0;">
                            <label style="font-size:10px;">Medication Name</label>
                            <input type="text" :name="'medication_name[' + index + ']'" placeholder="e.g. Amoxicillin" required>
                        </div>
                        <div class="field-group" style="margin:0;">
                            <label style="font-size:10px;">Dosage</label>
                            <input type="text" :name="'dosage[' + index + ']'" placeholder="e.g. 500mg">
                        </div>
                        <div class="field-group" style="margin:0;">
                            <label style="font-size:10px;">Quantity</label>
                            <input type="number" :name="'quantity[' + index + ']'" placeholder="e.g. 21" min="0">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:10px;margin-bottom:8px;">
                        <div class="field-group" style="margin:0;">
                            <label style="font-size:10px;">Frequency</label>
                            <input type="text" :name="'frequency[' + index + ']'" placeholder="e.g. 3x daily">
                        </div>
                        <div class="field-group" style="margin:0;">
                            <label style="font-size:10px;">Duration</label>
                            <input type="text" :name="'duration[' + index + ']'" placeholder="e.g. 7 days">
                        </div>
                        <div class="field-group" style="margin:0;">
                            <label style="font-size:10px;">Instructions</label>
                            <input type="text" :name="'instructions[' + index + ']'" placeholder="e.g. Take after meals">
                        </div>
                    </div>
                    <button type="button" x-show="rxItems.length > 1" @click="rxItems.splice(index, 1)" style="background:none;border:none;color:#D9534F;font-size:11px;cursor:pointer;padding:0;text-decoration:underline;text-underline-offset:2px;">Remove medication</button>
                </div>
            </template>

            <button type="button" class="btn-secondary" style="font-size:12px;padding:6px 14px;margin-bottom:14px;" @click="rxItems.push({})">
                + Add Another Medication
            </button>

            <div class="field-group" style="margin-bottom:14px;">
                <label for="rx_notes">Additional Notes <span class="optional-tag">optional</span></label>
                <textarea id="rx_notes" name="notes" rows="2" placeholder="Any general notes for this prescription"></textarea>
            </div>

            <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                <button type="submit" class="btn-primary">Save Prescription</button>
            </div>
        </form>
    </div>

    <!-- Past Prescriptions List -->
    @forelse($prescriptions ?? [] as $rx)
        <div style="border:1px solid var(--color-border, #E7ECEB);border-radius:10px;padding:14px 16px;margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                <div>
                    <div style="font-weight:600;font-size:13px;color:var(--color-ink);">
                        {{ $rx->date_issued->format('F d, Y') }}
                        <span class="status-tag status-{{ $rx->status === 'active' ? 'confirmed' : ($rx->status === 'completed' ? 'completed' : 'cancelled') }}" style="margin-left:8px;font-size:10px;">{{ ucfirst($rx->status) }}</span>
                    </div>
                    <div style="font-size:11px;color:var(--color-muted);margin-top:2px;">
                        {{ $rx->dentist->name ?? 'Dentist not specified' }} &middot; {{ $rx->items->count() }} medication{{ $rx->items->count() !== 1 ? 's' : '' }}
                    </div>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('prescriptions.print', $rx) }}" target="_blank" class="pill-btn">Print</a>
                    <form method="POST" action="{{ route('prescriptions.destroy', $rx) }}" onsubmit="return confirm('Delete this prescription?')" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pill-btn pill-btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($rx->items as $item)
                    <span style="font-size:11px;background:var(--color-bg);padding:3px 10px;border-radius:12px;color:var(--color-ink);">
                        {{ $item->medication_name }}{{ $item->dosage ? ' ' . $item->dosage : '' }}
                    </span>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align:center;color:var(--color-muted);font-size:13px;padding:20px 0;">No prescriptions recorded yet.</p>
    @endforelse

</div>

<div class="panel profile-bottom-panel" style="margin-top:1.5rem;" x-data="{ showUploadForm: false, lightboxUrl: null }">

    <div class="panel-header">
        <h2 class="panel-title">X-Rays &amp; Images</h2>
        <button type="button" class="btn-primary" style="font-size:12px;padding:6px 14px;" @click="showUploadForm = !showUploadForm">
            <span x-text="showUploadForm ? 'Cancel' : '+ Upload Image'"></span>
        </button>
    </div>

    <!-- Upload Form -->
    <div x-show="showUploadForm" x-cloak style="margin-bottom:1.5rem;padding:16px;background:var(--color-bg);border-radius:10px;">
        <form method="POST" action="{{ route('patients.images.store', $patient) }}" enctype="multipart/form-data" class="clinic-form">
            @csrf

            <div class="form-grid" style="margin-bottom:12px;">
                <div class="field-group">
                    <label for="img_type">Type</label>
                    <select id="img_type" name="type" required>
                        <option value="xray">X-Ray</option>
                        <option value="photo">Clinical Photo</option>
                        <option value="document">Document Scan</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="field-group">
                    <label for="img_taken_date">Date Taken</label>
                    <input id="img_taken_date" type="date" name="taken_date" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="field-group field-group-full">
                    <label for="img_label">Label <span class="optional-tag">optional</span></label>
                    <input id="img_label" type="text" name="label" placeholder="e.g. Upper right molar, before extraction">
                </div>
                <div class="field-group field-group-full">
                    <label for="img_files">Select Image(s)</label>
                    <input id="img_files" type="file" name="images[]" accept="image/*" multiple required>
                    <div style="font-size:11px;color:var(--color-muted);margin-top:4px;">JPG, PNG, WEBP. Max 10MB per file. You can select multiple at once.</div>
                </div>
            </div>

            <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                <button type="submit" class="btn-primary">Upload</button>
            </div>
        </form>
    </div>

    <!-- Image Gallery Grid -->
    @forelse(($images ?? collect())->groupBy('type') as $type => $typeImages)
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);margin-bottom:10px;">
                {{ ucfirst($type) }}s ({{ $typeImages->count() }})
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));gap:12px;">
                @foreach($typeImages as $img)
                    <div style="border:1px solid var(--color-border, #E7ECEB);border-radius:10px;overflow:hidden;background:var(--color-surface);">
                        <div @click="lightboxUrl = '{{ asset('storage/' . $img->file_path) }}'"
                             style="height:110px;background-image:url('{{ asset('storage/' . $img->file_path) }}');background-size:cover;background-position:center;cursor:pointer;"></div>
                        <div style="padding:8px 10px;">
                            <div style="font-size:11px;font-weight:600;color:var(--color-ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $img->label }}">
                                {{ $img->label ?: 'Untitled' }}
                            </div>
                            <div style="font-size:10px;color:var(--color-muted);margin-top:2px;display:flex;justify-content:space-between;align-items:center;">
                                <span>{{ $img->taken_date?->format('M d, Y') }}</span>
                                <form method="POST" action="{{ route('patients.images.destroy', $img) }}" onsubmit="return confirm('Delete this image?')" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;color:#D9534F;font-size:10px;cursor:pointer;padding:0;text-decoration:underline;text-underline-offset:2px;">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p style="text-align:center;color:var(--color-muted);font-size:13px;padding:20px 0;">No images uploaded yet.</p>
    @endforelse

    <!-- Lightbox -->
    <div x-show="lightboxUrl" x-cloak
         style="position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;display:flex;align-items:center;justify-content:center;padding:30px;"
         @click.self="lightboxUrl = null">
        <img :src="lightboxUrl" style="max-width:90%;max-height:90%;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
        <button type="button" @click="lightboxUrl = null" style="position:absolute;top:20px;right:30px;background:none;border:none;color:white;font-size:32px;cursor:pointer;line-height:1;">&times;</button>
    </div>

</div>

</x-clinic-layout>