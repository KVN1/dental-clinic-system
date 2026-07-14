<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('bg_color')->default('#F7F9F9')->after('theme_preset');
            $table->string('surface_color')->default('#FFFFFF')->after('bg_color');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn(['bg_color', 'surface_color']);
        });
    }
};
