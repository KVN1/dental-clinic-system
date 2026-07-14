<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        // Existing
        'backup_interval_hours',
        'backup_frequency_type',
        'backup_time',
        'backup_external_path',
        'last_backup_at',
        // New clinic settings
        'clinic_name',
        'tagline',
        'logo',
        'favicon',
        'address',
        'phone',
        'email',
        'website',
        'tin',
        'currency_symbol',
        'currency_code',
        'date_format',
        'time_format',
        'timezone',
        'payment_methods',
        'default_tax_rate',
        'show_tax_on_receipt',
        'primary_color',
        'receipt_footer_note',
    ];

    protected $casts = [
        'last_backup_at'       => 'datetime',
        'payment_methods'      => 'array',
        'show_tax_on_receipt'  => 'boolean',
        'default_tax_rate'     => 'decimal:2',
    ];

    public static function current()
    {
        return static::firstOrCreate(['id' => 1], [
            'clinic_name'    => 'Clear Smile Dental Clinic',
            'currency_symbol' => '₱',
            'currency_code'  => 'PHP',
            'date_format'    => 'M d, Y',
            'time_format'    => 'h:i A',
            'timezone'       => 'Asia/Manila',
            'primary_color'  => '#1e4a8a',
            'backup_frequency_type' => 'daily',
            'backup_time'    => '02:00',
        ]);
    }
}
