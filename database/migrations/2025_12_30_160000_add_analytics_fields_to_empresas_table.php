<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Google Analytics 4
            $table->string('ga4_measurement_id', 50)->nullable()->after('whatsapp')
                ->comment('Google Analytics 4 Measurement ID (G-XXXXXXXXXX)');

            // Facebook Pixel
            $table->string('fb_pixel_id', 50)->nullable()->after('ga4_measurement_id')
                ->comment('Facebook Pixel ID');

            // Google Tag Manager (opcional)
            $table->string('gtm_container_id', 50)->nullable()->after('fb_pixel_id')
                ->comment('Google Tag Manager Container ID (GTM-XXXXXXX)');

            // WhatsApp Business API
            $table->string('whatsapp_api_token')->nullable()->after('gtm_container_id')
                ->comment('Token de acceso para WhatsApp Business API');
            $table->string('whatsapp_phone_id', 50)->nullable()->after('whatsapp_api_token')
                ->comment('Phone Number ID de WhatsApp Business');
            $table->string('whatsapp_business_id', 50)->nullable()->after('whatsapp_phone_id')
                ->comment('Business Account ID de WhatsApp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'ga4_measurement_id',
                'fb_pixel_id',
                'gtm_container_id',
                'whatsapp_api_token',
                'whatsapp_phone_id',
                'whatsapp_business_id',
            ]);
        });
    }
};
