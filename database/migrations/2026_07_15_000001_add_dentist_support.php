<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add dentist-specific fields to users (role already exists: admin/staff -> now also 'dentist')
        Schema::table('users', function (Blueprint $table) {
            $table->string('specialty')->nullable()->after('role');
            $table->string('color')->nullable()->after('specialty'); // for calendar color coding
            $table->boolean('is_active')->default(true)->after('color');
        });

        // Link appointments to a dentist
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('dentist_id')->nullable()->after('patient_id')
                  ->constrained('users')->nullOnDelete();
        });

        // Link patient logs (treatments) to the dentist who performed them
        Schema::table('patient_logs', function (Blueprint $table) {
            $table->foreignId('dentist_id')->nullable()->after('recorded_by')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patient_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dentist_id');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dentist_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['specialty', 'color', 'is_active']);
        });
    }
};
