<x-clinic-layout :title="'Edit Appointment'">

    <div class="form-wrapper">
        <div class="panel">
            <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="clinic-form">
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