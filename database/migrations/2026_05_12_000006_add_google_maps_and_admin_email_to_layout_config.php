<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->string('google_maps_api_key', 255)->nullable()->after('default_og_image_path');
            $table->string('google_maps_country', 5)->nullable()->default('au')->after('google_maps_api_key');
            $table->string('admin_notification_email', 150)->nullable()->after('google_maps_country');
        });
    }

    public function down()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->dropColumn(['google_maps_api_key', 'google_maps_country', 'admin_notification_email']);
        });
    }
};
