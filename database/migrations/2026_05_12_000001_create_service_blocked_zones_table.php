<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_blocked_zones', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['polygon', 'postcode', 'suburb']);
            $table->string('name', 150);
            $table->json('polygon_coordinates')->nullable();
            $table->string('postcode', 20)->nullable();
            $table->string('suburb', 150)->nullable();
            $table->string('state', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('postcode');
            $table->index('suburb');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_blocked_zones');
    }
};
