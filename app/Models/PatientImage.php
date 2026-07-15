<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientImage extends Model
{
    protected $fillable = [
        'patient_id',
        'uploaded_by',
        'file_path',
        'type',
        'label',
        'taken_date',
    ];

    protected $casts = [
        'taken_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
