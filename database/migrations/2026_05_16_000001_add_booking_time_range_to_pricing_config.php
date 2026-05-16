<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('landing_pricing_config', function (Blueprint $table) {
            $table->time('booking_time_start')->default('08:00:00')->after('recurring_biweekly_discount');
            $table->time('booking_time_end')->default('20:00:00')->after('booking_time_start');
        });
    }

    public function down()
    {
        Schema::table('landing_pricing_config', function (Blueprint $table) {
            $table->dropColumn(['booking_time_start', 'booking_time_end']);
        });
    }
};
