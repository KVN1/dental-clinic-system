<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\Appointment;
use App\Models\AppointmentReschedule;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\PatientImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FactoryReset extends Command
{
    protected $signature = 'system:factory-reset {--keep-admin : Keep the first admin account instead of removing all users}';
    protected $description = 'Wipes ALL data (patients, appointments, demo data, everything) for a clean install before delivering to a buyer';

    public function handle()
    {
        if (!$this->confirm('This will PERMANENTLY delete ALL patients, appointments, prescriptions, images, and optionally user accounts. Are you sure?')) {
            $this->info('Cancelled.');
            return;
        }

        $this->info('Wiping patient data...');

        foreach (PatientImage::all() as $img) {
            Storage::disk('public')->delete($img->file_path);
        }

        AppointmentReschedule::query()->delete();
        PrescriptionItem::query()->delete();
        Prescription::query()->delete();
        PatientImage::query()->delete();
        PatientLog::query()->delete();
        Appointment::query()->delete();
        Patient::query()->delete();

        if (!$this->option('keep-admin')) {
            $this->info('Wiping user accounts (except keeping structure intact)...');
            \App\Models\User::query()->delete();
            $this->warn('All user accounts removed. You will need to create a new admin account on next launch.');
        }

        $this->info('Factory reset complete. System is ready to hand over to a new owner.');
    }
}
