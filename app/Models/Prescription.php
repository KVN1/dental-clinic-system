<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id',
        'dentist_id',
        'date_issued',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_issued' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function dentist()
    {
        return $this->belongsTo(User::class, 'dentist_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
