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
            // Agregar nuevos campos
            $table->integer('num_other_rooms')->nullable()->after('other_rooms');
            $table->string('other_rooms_desc')->nullable()->after('num_other_rooms');
            $table->decimal('service_type_price', 10, 2)->nullable()->after('base_price');
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
            $table->dropColumn(['num_other_rooms', 'other_rooms_desc', 'service_type_price']);
        });
    }
};
