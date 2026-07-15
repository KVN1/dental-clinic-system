<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'nickname',
        'birthdate',
        'sex',
        'contact_number',
        'email',
        'address',
        'occupation',
        'referred_by',
        'reason_for_consultation',
        'previous_dentist',
        'last_dental_visit',
        'emergency_contact_name',
        'emergency_contact_number',
        'allergies',
        'medical_conditions',
        'medications',
        'physician_name',
        'physician_specialty',
        'blood_type',
        'blood_pressure',
        'medical_conditions_checklist',
        'balance',
    ];

    protected $casts = [
        'medical_conditions_checklist' => 'array',
    ];

    public function logs()
    {
        return $this->hasMany(PatientLog::class)->orderBy('visit_date', 'desc');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class)->orderBy('appointment_date')->orderBy('appointment_time');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class)->orderBy('date_issued', 'desc');
    }
}