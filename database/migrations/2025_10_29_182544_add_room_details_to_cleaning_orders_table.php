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
            // Remover square_footage_range y agregar nuevos campos
            $table->integer('num_bathrooms')->nullable()->after('time_flexible');
            $table->integer('num_bedrooms')->nullable()->after('num_bathrooms');
            $table->integer('num_kitchens')->nullable()->after('num_bedrooms');
            $table->string('other_rooms')->nullable()->after('num_kitchens');
            $table->integer('num_cleaners')->nullable()->after('other_rooms');
            $table->integer('num_hours')->nullable()->after('num_cleaners');
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
            $table->dropColumn([
                'num_bathrooms',
                'num_bedrooms',
                'num_kitchens',
                'other_rooms',
                'num_cleaners',
                'num_hours'
            ]);
        });
    }
};
