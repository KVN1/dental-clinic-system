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

    // True if this user should appear in dentist selectors/calendars -
    // either they're explicitly a dentist, OR they're an admin who also treats
    // patients (owner-dentist) and has filled in a specialty.
    public function isTreatingProvider()
    {
        return $this->role === 'dentist' || ($this->role === 'admin' && !empty($this->specialty));
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

    // Scope to pull anyone who can be assigned as a treating provider -
    // dentists, plus admins who've set a specialty (owner-dentists)
    public function scopeDentists($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('role', 'dentist')
                  ->orWhere(function ($q2) {
                      $q2->where('role', 'admin')->whereNotNull('specialty');
                  });
            });
    }
}