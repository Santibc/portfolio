<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->string('google_search_console_verification', 255)->nullable()->after('footer_logo_path');
            $table->string('default_og_image_path', 500)->nullable()->after('google_search_console_verification');
        });
    }

    public function down()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->dropColumn([
                'google_search_console_verification',
                'default_og_image_path',
            ]);
        });
    }
};
