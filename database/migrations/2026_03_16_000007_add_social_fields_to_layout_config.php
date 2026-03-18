<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->string('whatsapp_url')->nullable()->after('linkedin_url');
            $table->string('tiktok_url')->nullable()->after('whatsapp_url');
        });
    }

    public function down(): void
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_url', 'tiktok_url']);
        });
    }
};
