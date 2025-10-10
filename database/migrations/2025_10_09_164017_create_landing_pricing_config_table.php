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
        Schema::create('landing_pricing_config', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_number')->default('573202230467');
            $table->decimal('extra_heavy_duty', 8, 2)->default(150.00);
            $table->decimal('inside_fridge_ea', 8, 2)->default(50.00);
            $table->decimal('inside_oven_ea', 8, 2)->default(50.00);
            $table->decimal('post_construction_government', 8, 2)->default(0.90);
            $table->decimal('post_construction_private', 8, 2)->default(0.60);
            $table->decimal('window_clean_interior', 8, 2)->default(8.00);
            $table->decimal('window_clean_exterior', 8, 2)->default(10.00);
            $table->integer('recurring_weekly_discount')->default(20);
            $table->integer('recurring_biweekly_discount')->default(15);
            $table->timestamps();
        });

        // Tabla para los rangos de square footage y precios
        Schema::create('landing_pricing_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('sq_ft_min');
            $table->integer('sq_ft_max');
            $table->decimal('initial_clean', 8, 2);
            $table->decimal('weekly', 8, 2);
            $table->decimal('biweekly', 8, 2);
            $table->decimal('monthly', 8, 2);
            $table->decimal('deep_clean', 8, 2);
            $table->decimal('move_out_clean', 8, 2);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_pricing_ranges');
        Schema::dropIfExists('landing_pricing_config');
    }
};
