<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siigo_config', function (Blueprint $table) {
            $table->id();
            $table->string('username', 150)->nullable();
            $table->text('access_key')->nullable();
            $table->string('partner_id', 100)->nullable();
            $table->enum('ambiente', ['sandbox', 'produccion'])->default('sandbox');
            $table->text('token_cache')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('sync_catalogos_at')->nullable();
            $table->boolean('activo')->default(false);
            $table->timestamps();
        });

        Schema::create('siigo_catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 40);
            $table->string('codigo', 80);
            $table->string('nombre', 200);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['tipo', 'codigo']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siigo_catalogos');
        Schema::dropIfExists('siigo_config');
    }
};
