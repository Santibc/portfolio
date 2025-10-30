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
        Schema::table('cleaning_orders', function (Blueprint $table) {
            $table->string('square_footage_range')->nullable()->change();
            $table->string('service_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cleaning_orders', function (Blueprint $table) {
            $table->string('square_footage_range')->nullable(false)->change();
            $table->string('service_type')->nullable(false)->change();
        });
    }
};
