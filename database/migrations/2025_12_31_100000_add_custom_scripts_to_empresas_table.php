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
            // Scripts personalizados para el head (Google Ads, TikTok Pixel, etc.)
            $table->text('custom_scripts_head')->nullable()->after('gtm_container_id')
                ->comment('Scripts personalizados para insertar en el <head>');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('custom_scripts_head');
        });
    }
};
