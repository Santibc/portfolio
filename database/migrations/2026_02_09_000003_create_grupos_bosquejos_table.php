<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grupos_bosquejos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grupos_bosquejos');
    }
};
