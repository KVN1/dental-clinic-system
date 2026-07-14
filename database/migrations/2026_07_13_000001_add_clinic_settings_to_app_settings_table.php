<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            // Clinic Identity
            $table->string('clinic_name')->default('Clear Smile Dental Clinic')->after('id');
            $table->string('tagline')->nullable()->after('clinic_name');
            $table->string('logo')->nullable()->after('tagline');
            $table->string('favicon')->nullable()->after('logo');

            // Contact Info (shown on receipts/reports)
            $table->string('address')->nullable()->after('favicon');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->string('tin')->nullable()->after('website');

            // System Preferences
            $table->string('currency_symbol')->default('₱')->after('tin');
            $table->string('currency_code')->default('PHP')->after('currency_symbol');
            $table->string('date_format')->default('M d, Y')->after('currency_code');
            $table->string('time_format')->default('h:i A')->after('date_format');
            $table->string('timezone')->default('Asia/Manila')->after('time_format');

            // Billing
            $table->json('payment_methods')->nullable()->after('timezone');
            $table->decimal('default_tax_rate', 5, 2)->default(0)->after('payment_methods');
            $table->boolean('show_tax_on_receipt')->default(false)->after('default_tax_rate');

            // Appearance
            $table->string('primary_color')->default('#1e4a8a')->after('show_tax_on_receipt');
            $table->string('receipt_footer_note')->nullable()->after('primary_color');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'clinic_name', 'tagline', 'logo', 'favicon',
                'address', 'phone', 'email', 'website', 'tin',
                'currency_symbol', 'currency_code', 'date_format',
                'time_format', 'timezone', 'payment_methods',
                'default_tax_rate', 'show_tax_on_receipt',
                'primary_color', 'receipt_footer_note',
            ]);
        });
    }
};
