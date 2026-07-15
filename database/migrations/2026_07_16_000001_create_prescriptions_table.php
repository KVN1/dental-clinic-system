<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dentist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_issued');
            $table->text('notes')->nullable(); // general notes for the whole prescription slip
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('medication_name');
            $table->string('dosage')->nullable();       // e.g. "500mg"
            $table->string('frequency')->nullable();    // e.g. "3x daily"
            $table->string('duration')->nullable();     // e.g. "7 days"
            $table->text('instructions')->nullable();   // e.g. "Take after meals"
            $table->integer('quantity')->nullable();    // e.g. 21 tablets
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};
