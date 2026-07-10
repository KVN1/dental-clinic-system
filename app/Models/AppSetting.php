<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'backup_interval_hours',
        'backup_frequency_type',
        'backup_time',
        'backup_external_path',
        'last_backup_at',
    ];

    protected $casts = [
        'last_backup_at' => 'datetime',
    ];

    public static function current()
    {
        return static::firstOrCreate(['id' => 1]);
    }
}