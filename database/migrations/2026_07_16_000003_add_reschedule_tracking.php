<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->integer('reschedule_count')->default(0)->after('status');
        });

        Schema::create('appointment_reschedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->date('old_date');
            $table->time('old_time')->nullable();
            $table->date('new_date');
            $table->time('new_time')->nullable();
            $table->string('reason')->nullable();
            $table->foreignId('rescheduled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_reschedules');
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('reschedule_count');
        });
    }
};
