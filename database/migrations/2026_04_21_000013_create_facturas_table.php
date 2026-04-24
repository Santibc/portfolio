<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_interno', 30)->unique();
            $table->string('numero_siigo', 30)->nullable();
            $table->string('cufe', 150)->nullable();
            $table->string('xml_firmado_path', 255)->nullable();

            $table->date('fecha');
            $table->date('vencimiento')->nullable();

            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('moneda_id')->constrained('monedas')->restrictOnDelete();
            $table->decimal('tasa_cambio', 14, 4)->nullable();

            $table->decimal('subtotal', 16, 2)->default(0);
            $table->decimal('descuento_total', 16, 2)->default(0);
            $table->decimal('iva_total', 16, 2)->default(0);
            $table->decimal('flete', 16, 2)->default(0);
            $table->decimal('seguro', 16, 2)->default(0);
            $table->decimal('total', 16, 2)->default(0);

            $table->decimal('total_cop', 16, 2)->nullable();

            $table->text('observaciones')->nullable();
            $table->enum('estado', ['borrador', 'emitida', 'enviada', 'pagada', 'anulada'])->default('borrador');
            $table->boolean('es_electronica')->default(false);

            $table->foreignId('plantilla_factura_id')->nullable()->constrained('plantillas_factura')->nullOnDelete();
            $table->string('pdf_path', 255)->nullable();
            $table->string('token_publico', 64)->unique();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('emitida_at')->nullable();
            $table->timestamp('enviada_at')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha');
            $table->index(['cliente_id', 'estado']);
            $table->index('es_electronica');
        });

        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('referencia', 40);
            $table->string('descripcion', 200);
            $table->string('color', 60)->nullable();
            $table->string('composicion', 255)->nullable();
            $table->string('codigo_pa', 20)->nullable();
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 14, 2);
            $table->decimal('descuento', 14, 2)->default(0);
            $table->decimal('impuesto_porcentaje', 5, 2)->default(0);
            $table->decimal('total_linea', 16, 2);
            $table->json('tallas_json')->nullable();
            $table->smallInteger('orden')->default(0);
            $table->timestamps();

            $table->index('factura_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_items');
        Schema::dropIfExists('facturas');
    }
};
