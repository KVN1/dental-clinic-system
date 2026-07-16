<x-clinic-layout :title="'Edit Appointment'">

    <div class="form-wrapper">
        <div class="panel">
            <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="clinic-form">

                @if($appointment->reschedule_count > 0)
                    <div style="background:#FFF7ED;border:1px solid #FDBA74;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#9A3412;">
                        <strong>Rescheduled {{ $appointment->reschedule_count }}x</strong>
                        <div style="margin-top:6px;">
                            @foreach($appointment->reschedules as $r)
                                <div style="margin-bottom:4px;">
                                    {{ $r->old_date->format('M d') }} {{ $r->old_time ? \Carbon\Carbon::parse($r->old_time)->format('g:i A') : '' }}
                                    &rarr;
                                    {{ \Carbon\Carbon::parse($r->new_date)->format('M d') }} {{ $r->new_time ? \Carbon\Carbon::parse($r->new_time)->format('g:i A') : '' }}
                                    @if($r->reason)
                                        <span style="color:#C2410C;">&middot; {{ $r->reason }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field-group field-group-full">
                        <label for="patient_id">Patient</label>
                        <select id="patient_id" name="patient_id" required>
                            <option value="">— Select Patient —</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->last_name }}, {{ $patient->first_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="dentist_id">Dentist</label>
                        <select id="dentist_id" name="dentist_id">
                            <option value="">— Not specified —</option>
                            @foreach ($dentists as $dentist)
                                <option value="{{ $dentist->id }}" {{ old('dentist_id', $appointment->dentist_id) == $dentist->id ? 'selected' : '' }}>
                                    {{ $dentist->name }}{{ $dentist->specialty ? ' — '.$dentist->specialty : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('dentist_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="appointment_date">Date</label>
                        <input id="appointment_date" type="date" name="appointment_date" value="{{ old('appointment_date', optional($appointment->appointment_date)->format('Y-m-d')) }}" required>
                        @error('appointment_date') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="appointment_time">Time</label>
                        <input id="appointment_time" type="time" name="appointment_time" value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i')) }}" required>
                        @error('appointment_time') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group field-group-full">
                        <label for="reschedule_reason">Reason for Change <span class="optional-tag">only needed if changing date/time</span></label>
                        <input id="reschedule_reason" type="text" name="reschedule_reason" placeholder="e.g. Patient requested, Dentist unavailable, Emergency">
                    </div>

                    <div class="field-group field-group-full" x-data="{
                        purposeText: '{{ old('purpose', $appointment->purpose) }}',
                        options: ['General Checkup', 'Cleaning / Prophylaxis', 'Tooth Extraction (Removal)', 'Filling / Pasta', 'Root Canal Treatment', 'Tooth Whitening', 'Braces Adjustment', 'Denture Fitting', 'X-Ray', 'Consultation', 'Follow-up', 'Balance Payment'],
                        isSelected(opt) {
                            return this.purposeText.split(',').map(s => s.trim()).includes(opt);
                        },
                        toggle(opt) {
                            let parts = this.purposeText.split(',').map(s => s.trim()).filter(Boolean);
                            if (this.isSelected(opt)) {
                                parts = parts.filter(p => p !== opt);
                            } else {
                                parts.push(opt);
                            }
                            this.purposeText = parts.join(', ');
                        }
                    }">
                        <label for="purpose">Purpose</label>
                        <input
                            id="purpose"
                            type="text"
                            name="purpose"
                            x-model="purposeText"
                            placeholder="e.g. Cleaning, Follow-up, Consultation"
                            autocomplete="off"
                        >
                        <div class="purpose-chips">
                            <template x-for="opt in options" :key="opt">
                                <button
                                    type="button"
                                    class="purpose-chip"
                                    :class="isSelected(opt) && 'selected'"
                                    @click="toggle(opt)"
                                    x-text="opt"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <div class="field-group field-group-full">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="2">{{ old('notes', $appointment->notes) }}</textarea>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('appointments.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</x-clinic-layout>