<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('nickname')->nullable()->after('last_name');
            $table->string('occupation')->nullable()->after('address');
            $table->string('referred_by')->nullable()->after('occupation');
            $table->text('reason_for_consultation')->nullable()->after('referred_by');
            $table->string('previous_dentist')->nullable()->after('reason_for_consultation');
            $table->date('last_dental_visit')->nullable()->after('previous_dentist');
            $table->string('physician_name')->nullable()->after('medications');
            $table->string('physician_specialty')->nullable()->after('physician_name');
            $table->string('blood_type')->nullable()->after('physician_specialty');
            $table->string('blood_pressure')->nullable()->after('blood_type');
            $table->text('medical_conditions_checklist')->nullable()->after('blood_pressure');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name', 'nickname', 'occupation', 'referred_by',
                'reason_for_consultation', 'previous_dentist', 'last_dental_visit',
                'physician_name', 'physician_specialty', 'blood_type', 'blood_pressure',
                'medical_conditions_checklist',
            ]);
        });
    }
};