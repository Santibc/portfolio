<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores')->cascadeOnDelete();
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->decimal('salario_bruto', 12, 2)->default(0);
            $table->decimal('ss_empresa', 12, 2)->default(0);   // SS a cargo de la empresa
            $table->decimal('ss_trabajador', 12, 2)->default(0); // SS a cargo del trabajador
            $table->decimal('irpf', 12, 2)->default(0);
            $table->decimal('liquido', 12, 2)->default(0);       // líquido percibido por el trabajador
            $table->string('documento_path')->nullable();        // PDF de la nómina
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['trabajador_id', 'anio', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};
