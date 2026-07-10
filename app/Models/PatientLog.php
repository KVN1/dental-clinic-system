<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientLog extends Model
{
    protected $fillable = [
        'patient_id',
        'visit_date',
        'tooth_number',
        'entry_type',
        'description',
        'amount_charged',
        'amount_paid',
        'recorded_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}