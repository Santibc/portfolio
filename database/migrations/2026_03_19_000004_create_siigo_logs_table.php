<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('siigo_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_siigo_id')->nullable()
                ->constrained('facturas_siigo')->nullOnDelete();
            $table->string('endpoint');
            $table->string('method', 10);
            $table->json('request_body')->nullable();
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->json('response_body')->nullable();
            $table->unsignedInteger('duracion_ms')->nullable();
            $table->boolean('exitoso')->default(false);
            $table->text('error_mensaje')->nullable();
            $table->foreignId('usuario_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('siigo_logs');
    }
};
