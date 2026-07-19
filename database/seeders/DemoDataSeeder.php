<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\Appointment;
use App\Models\AppointmentReschedule;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\PatientImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    /**
     * Clears out real patient/appointment/prescription data and replaces it
     * with realistic fake demo data. Does NOT touch clinic settings, theme,
     * or user login accounts - those stay exactly as configured.
     */
    public function run(): void
    {
        $this->command->info('Clearing existing patient data...');

        // Delete uploaded image files from storage before clearing the DB records
        foreach (PatientImage::all() as $img) {
            Storage::disk('public')->delete($img->file_path);
        }

        // Clear in dependency order (children first)
        AppointmentReschedule::query()->delete();
        PrescriptionItem::query()->delete();
        Prescription::query()->delete();
        PatientImage::query()->delete();
        PatientLog::query()->delete();
        Appointment::query()->delete();
        Patient::query()->delete();

        $this->command->info('Seeding demo patients...');

        $dentist = \App\Models\User::dentists()->first();

        $demoPatients = [
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'sex' => 'Female', 'birthdate' => '1990-03-15', 'contact_number' => '0917 123 4567', 'email' => 'maria.santos@example.com', 'address' => '123 Session Road, Baguio City', 'allergies' => 'Penicillin', 'balance' => 1500],
            ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'sex' => 'Male', 'birthdate' => '1985-07-22', 'contact_number' => '0918 234 5678', 'email' => 'juan.delacruz@example.com', 'address' => '45 Magsaysay Ave, La Trinidad', 'allergies' => 'None reported', 'balance' => 0],
            ['first_name' => 'Angela', 'last_name' => 'Reyes', 'sex' => 'Female', 'birthdate' => '1998-11-02', 'contact_number' => '0919 345 6789', 'email' => 'angela.reyes@example.com', 'address' => '78 Loakan Road, Baguio City', 'allergies' => 'Latex', 'balance' => 3200],
            ['first_name' => 'Marco', 'last_name' => 'Villanueva', 'sex' => 'Male', 'birthdate' => '2001-01-30', 'contact_number' => '0920 456 7890', 'email' => 'marco.villanueva@example.com', 'address' => '12 Kisad Road, Baguio City', 'allergies' => 'None reported', 'balance' => 0],
            ['first_name' => 'Bea', 'last_name' => 'Fernandez', 'sex' => 'Female', 'birthdate' => '1975-09-18', 'contact_number' => '0921 567 8901', 'email' => 'bea.fernandez@example.com', 'address' => '34 Naguilian Road, Baguio City', 'allergies' => 'Sulfa drugs', 'balance' => 850],
            ['first_name' => 'Ramon', 'last_name' => 'Aquino', 'sex' => 'Male', 'birthdate' => '1993-05-10', 'contact_number' => '0922 678 9012', 'email' => 'ramon.aquino@example.com', 'address' => '56 Marcos Highway, Baguio City', 'allergies' => 'None reported', 'balance' => 0],
            ['first_name' => 'Kristine', 'last_name' => 'Domingo', 'sex' => 'Female', 'birthdate' => '2005-12-25', 'contact_number' => '0923 789 0123', 'email' => 'kristine.domingo@example.com', 'address' => '89 South Drive, Baguio City', 'allergies' => 'None reported', 'balance' => 2000],
            ['first_name' => 'Paolo', 'last_name' => 'Mendoza', 'sex' => 'Male', 'birthdate' => '1988-04-14', 'contact_number' => '0924 890 1234', 'email' => 'paolo.mendoza@example.com', 'address' => '21 Leonard Wood Road, Baguio City', 'allergies' => 'Aspirin', 'balance' => 0],
        ];

        $patients = [];
        foreach ($demoPatients as $data) {
            $patients[] = Patient::create($data);
        }

        $this->command->info('Seeding demo logs...');

        $procedures = [
            ['desc' => 'Oral Prophylaxis / Cleaning', 'charge' => 1500, 'tooth' => null],
            ['desc' => 'Tooth Extraction', 'charge' => 2500, 'tooth' => '#18'],
            ['desc' => 'Composite Filling', 'charge' => 1800, 'tooth' => '#14'],
            ['desc' => 'Root Canal Treatment', 'charge' => 8000, 'tooth' => '#26'],
            ['desc' => 'Dental X-Ray', 'charge' => 500, 'tooth' => null],
            ['desc' => 'Consultation', 'charge' => 300, 'tooth' => null],
        ];

        foreach ($patients as $patient) {
            $numVisits = rand(1, 3);
            for ($i = 0; $i < $numVisits; $i++) {
                $proc = $procedures[array_rand($procedures)];
                $charged = $proc['charge'];
                $paid = rand(0, 1) ? $charged : round($charged * 0.6);

                PatientLog::create([
                    'patient_id'      => $patient->id,
                    'visit_date'      => now()->subDays(rand(5, 180)),
                    'tooth_number'    => $proc['tooth'],
                    'entry_type'      => 'visit',
                    'description'     => $proc['desc'],
                    'amount_charged'  => $charged,
                    'amount_paid'     => $paid,
                    'payment_method'  => ['Cash', 'GCash', 'Bank Transfer'][array_rand(['Cash', 'GCash', 'Bank Transfer'])],
                    'dentist_id'      => $dentist->id ?? null,
                    'recorded_by'     => 'Demo Staff',
                ]);
            }
        }

        $this->command->info('Seeding demo appointments...');

        $purposes = ['General Checkup', 'Cleaning / Prophylaxis', 'Follow-up', 'Consultation', 'Tooth Extraction (Removal)', 'X-Ray'];

        foreach ($patients as $index => $patient) {
            // A mix of past, today, and future appointments
            $daysOffset = [-3, 0, 1, 3, 7, 14][$index % 6];

            Appointment::create([
                'patient_id'        => $patient->id,
                'dentist_id'        => $dentist->id ?? null,
                'appointment_date'  => now()->addDays($daysOffset)->format('Y-m-d'),
                'appointment_time'  => sprintf('%02d:%02d:00', rand(9, 16), [0, 15, 30, 45][array_rand([0, 15, 30, 45])]),
                'purpose'           => $purposes[array_rand($purposes)],
                'status'            => $daysOffset < 0 ? 'completed' : 'scheduled',
            ]);
        }

        $this->command->info('Seeding demo prescriptions...');

        $medications = [
            ['name' => 'Amoxicillin', 'dosage' => '500mg', 'frequency' => '3x daily', 'duration' => '7 days', 'qty' => 21],
            ['name' => 'Mefenamic Acid', 'dosage' => '500mg', 'frequency' => 'Every 8 hours as needed', 'duration' => '5 days', 'qty' => 15],
            ['name' => 'Chlorhexidine Mouthwash', 'dosage' => '0.12%', 'frequency' => '2x daily', 'duration' => '14 days', 'qty' => 1],
        ];

        foreach (array_slice($patients, 0, 4) as $patient) {
            $rx = Prescription::create([
                'patient_id'  => $patient->id,
                'dentist_id'  => $dentist->id ?? null,
                'date_issued' => now()->subDays(rand(1, 20)),
                'status'      => 'active',
            ]);

            $med = $medications[array_rand($medications)];
            PrescriptionItem::create([
                'prescription_id' => $rx->id,
                'medication_name' => $med['name'],
                'dosage'          => $med['dosage'],
                'frequency'       => $med['frequency'],
                'duration'        => $med['duration'],
                'quantity'        => $med['qty'],
                'instructions'    => 'Take after meals.',
            ]);
        }

        $this->command->info('Demo data seeded successfully! ' . count($patients) . ' patients created.');
    }
}
