<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novedades_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('variante_producto_id')->nullable()->constrained('variantes_productos')->onDelete('restrict');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones')->onDelete('restrict');
            $table->enum('tipo', ['garantia', 'saldo', 'perdida', 'dano']);
            $table->integer('cantidad');
            $table->decimal('valor_original', 12, 2);
            $table->decimal('valor_saldo', 12, 2)->nullable();
            $table->text('descripcion');
            $table->enum('estado', ['pendiente', 'procesado', 'recuperado', 'dado_de_baja'])->default('pendiente');
            $table->string('numero_garantia')->nullable();
            $table->date('fecha_vencimiento_garantia')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('cerrado_en')->nullable();
            $table->text('notas_cierre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novedades_stock');
    }
};
