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
        Schema::table('landing_pricing_config', function (Blueprint $table) {
            $table->decimal('cleaner_price', 10, 2)->default(30)->after('id');
            $table->decimal('hour_price', 10, 2)->default(30)->after('cleaner_price');
            $table->decimal('normal_service_price', 10, 2)->default(0)->after('hour_price');
            $table->decimal('deep_service_price', 10, 2)->default(50)->after('normal_service_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_pricing_config', function (Blueprint $table) {
            $table->dropColumn(['cleaner_price', 'hour_price', 'normal_service_price', 'deep_service_price']);
        });
    }
};
