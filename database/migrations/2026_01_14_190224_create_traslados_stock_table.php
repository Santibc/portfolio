<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traslados_stock', function (Blueprint $table) {
            $table->id();
            $table->string('numero_traslado')->unique();
            $table->foreignId('ubicacion_origen_id')->constrained('ubicaciones')->onDelete('restrict');
            $table->foreignId('ubicacion_destino_id')->constrained('ubicaciones')->onDelete('restrict');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos')->onDelete('restrict');
            $table->integer('cantidad');
            $table->enum('estado', ['pendiente', 'en_transito', 'completado', 'cancelado'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->foreignId('usuario_creador_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('usuario_receptor_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('enviado_en')->nullable();
            $table->timestamp('recibido_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traslados_stock');
    }
};
