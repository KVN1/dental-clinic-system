<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('backup_frequency_type')->default('daily')->after('backup_interval_hours'); // 'daily' or 'hourly'
            $table->time('backup_time')->default('18:00:00')->after('backup_frequency_type'); // time of day, or start time for hourly
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['backup_frequency_type', 'backup_time']);
        });
    }
};