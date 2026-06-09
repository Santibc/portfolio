<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['nacional', 'internacional'])->default('nacional');
            $table->string('tipo_identificacion', 20)->nullable();
            $table->string('identificacion', 50)->nullable();
            $table->string('nombre', 200);
            $table->string('nombre_comercial', 200)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('direccion_facturacion', 255)->nullable();
            $table->string('direccion_envio', 255)->nullable();
            $table->string('pais', 80)->default('Colombia');
            $table->string('ciudad', 100)->nullable();
            $table->foreignId('moneda_preferida_id')->nullable()->constrained('monedas')->nullOnDelete();
            $table->foreignId('incoterm_id')->nullable()->constrained('incoterms')->nullOnDelete();
            $table->foreignId('puerto_id')->nullable()->constrained('puertos')->nullOnDelete();
            $table->foreignId('tipo_pago_id')->nullable()->constrained('tipos_pago')->nullOnDelete();
            $table->text('datos_bancarios_destino')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('siigo_id', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('tipo');
            $table->index(['activo', 'tipo']);
            $table->index('identificacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
