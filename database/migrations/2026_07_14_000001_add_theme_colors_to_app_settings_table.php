<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('secondary_color')->default('#FF8966')->after('primary_color');
            $table->string('theme_preset')->default('teal-coral')->after('secondary_color');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['secondary_color', 'theme_preset']);
        });
    }
};
