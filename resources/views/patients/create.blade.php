<x-clinic-layout :title="'Add Patient'">

    <div class="form-wrapper">
        <div class="panel">

            <form method="POST" action="{{ route('patients.store') }}" class="clinic-form">
                @csrf

                <div class="form-section">
                    <h3 class="form-section-title">Basic Information</h3>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="first_name">First Name</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-group">
                            <label for="middle_name">Middle Name</label>
                            <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}">
                        </div>

                        <div class="field-group">
                            <label for="last_name">Last Name</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-group">
                            <label for="nickname">Nickname</label>
                            <input id="nickname" type="text" name="nickname" value="{{ old('nickname') }}">
                        </div>

                        <div class="field-group">
                            <label for="birthdate">Birthdate</label>
                            <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}">
                        </div>

                        <div class="field-group">
                            <label for="sex">Sex</label>
                            <select id="sex" name="sex">
                                <option value="">— Select —</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="occupation">Occupation</label>
                            <input id="occupation" type="text" name="occupation" value="{{ old('occupation') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">Contact Details</h3>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="contact_number">Contact Number</label>
                            <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}">
                        </div>

                        <div class="field-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}">
                        </div>

                        <div class="field-group field-group-full">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        </div>

                        <div class="field-group">
                            <label for="emergency_contact_name">Emergency Contact Name</label>
                            <input id="emergency_contact_name" type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                        </div>

                        <div class="field-group">
                            <label for="emergency_contact_number">Emergency Contact Number</label>
                            <input id="emergency_contact_number" type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">Dental History</h3>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="referred_by">Referred By</label>
                            <input id="referred_by" type="text" name="referred_by" value="{{ old('referred_by') }}">
                        </div>

                        <div class="field-group">
                            <label for="previous_dentist">Previous Dentist</label>
                            <input id="previous_dentist" type="text" name="previous_dentist" value="{{ old('previous_dentist') }}">
                        </div>

                        <div class="field-group">
                            <label for="last_dental_visit">Last Dental Visit</label>
                            <input id="last_dental_visit" type="date" name="last_dental_visit" value="{{ old('last_dental_visit') }}">
                        </div>

                        <div class="field-group field-group-full">
                            <label for="reason_for_consultation">Reason for Consultation</label>
                            <textarea id="reason_for_consultation" name="reason_for_consultation" rows="2">{{ old('reason_for_consultation') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">Medical History</h3>
                    <div class="form-grid">
                        <div class="field-group field-group-full">
                            <label for="allergies">Allergies</label>
                            <textarea id="allergies" name="allergies" rows="2">{{ old('allergies') }}</textarea>
                        </div>

                        <div class="field-group field-group-full">
                            <label for="medications">Current Medications</label>
                            <textarea id="medications" name="medications" rows="2">{{ old('medications') }}</textarea>
                        </div>

                        <div class="field-group">
                            <label for="physician_name">Physician's Name</label>
                            <input id="physician_name" type="text" name="physician_name" value="{{ old('physician_name') }}">
                        </div>

                        <div class="field-group">
                            <label for="physician_specialty">Specialty</label>
                            <input id="physician_specialty" type="text" name="physician_specialty" value="{{ old('physician_specialty') }}">
                        </div>

                        <div class="field-group">
                            <label for="blood_type">Blood Type</label>
                            <input id="blood_type" type="text" name="blood_type" value="{{ old('blood_type') }}" placeholder="e.g. O+">
                        </div>

                        <div class="field-group">
                            <label for="blood_pressure">Blood Pressure</label>
                            <input id="blood_pressure" type="text" name="blood_pressure" value="{{ old('blood_pressure') }}" placeholder="e.g. 120/80">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="form-section-title">Medical Conditions Checklist</h3>
                    <p class="settings-sublabel" style="margin-bottom: 0.85rem;">Check any that apply.</p>

                    <div class="checklist-grid">
                        @php
                            $conditions = [
                                'High Blood Pressure', 'Low Blood Pressure', 'Epilepsy / Convulsions',
                                'AIDS or HIV Infection', 'Sexually Transmitted Disease', 'Stomach Troubles / Ulcers',
                                'Fainting Seizure', 'Rapid Weight Loss', 'Radiation Therapy',
                                'Joint Replacement / Implant', 'Heart Surgery', 'Heart Attack',
                                'Thyroid Problem', 'Heart Disease', 'Heart Murmur',
                                'Hepatitis / Liver Disease', 'Rheumatic Fever', 'Hay Fever / Allergies',
                                'Respiratory Problems', 'Hepatitis / Jaundice', 'Tuberculosis',
                                'Swollen Ankles', 'Kidney Disease', 'Diabetes',
                                'Chest Pain', 'Stroke', 'Cancer / Tumors',
                                'Anemia', 'Angina', 'Asthma',
                                'Emphysema', 'Bleeding Problems', 'Blood Diseases',
                                'Head Injuries', 'Arthritis / Rheumatism',
                            ];
                        @endphp

                        @foreach ($conditions as $condition)
                            <label class="checklist-item">
                                <input type="checkbox" name="medical_conditions_checklist[]" value="{{ $condition }}" {{ in_array($condition, old('medical_conditions_checklist', [])) ? 'checked' : '' }}>
                                <span>{{ $condition }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="field-group field-group-full" style="margin-top:1rem;">
                        <label for="medical_conditions">Other Conditions <span class="optional-tag">not listed above</span></label>
                        <textarea id="medical_conditions" name="medical_conditions" rows="2" placeholder="Any other condition not covered by the checklist">{{ old('medical_conditions') }}</textarea>
                    </div>
                </div>

                <div class="form-footer">
                    <a href="{{ route('patients.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">Save Patient</button>
                </div>
            </form>

        </div>
    </div>

</x-clinic-layout>