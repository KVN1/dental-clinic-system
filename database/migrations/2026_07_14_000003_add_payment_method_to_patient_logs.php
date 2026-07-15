<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_logs', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('patient_logs', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
