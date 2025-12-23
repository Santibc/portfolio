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
            $table->enum('tipo', ['publico', 'privado']);
            $table->string('nombre_comercial', 255);
            $table->string('razon_social', 255)->nullable();
            $table->string('cif', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('pais', 100)->default('España');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('persona_contacto', 150)->nullable();
            $table->string('telefono_contacto', 20)->nullable();
            $table->string('email_contacto', 255)->nullable();
            $table->string('condiciones_pago', 100)->nullable()->comment('Ej: 30 días, 60 días');
            $table->decimal('retencion_porcentaje', 5, 2)->default(0)->comment('% de retención en obras');
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
