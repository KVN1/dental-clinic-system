<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDentist()
    {
        return $this->role === 'dentist';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'theme',
        'specialty',
        'color',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'dentist_id');
    }

    public function patientLogs()
    {
        return $this->hasMany(PatientLog::class, 'dentist_id');
    }

    // Scope to only pull dentist accounts, for dropdowns etc.
    public function scopeDentists($query)
    {
        return $query->where('role', 'dentist')->where('is_active', true);
    }
}
