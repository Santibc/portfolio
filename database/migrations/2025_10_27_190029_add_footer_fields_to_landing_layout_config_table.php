<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->text('footer_description')->nullable()->after('copyright_company');
            $table->string('footer_logo_path')->nullable()->after('footer_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_layout_config', function (Blueprint $table) {
            $table->dropColumn(['footer_description', 'footer_logo_path']);
        });
    }
};
