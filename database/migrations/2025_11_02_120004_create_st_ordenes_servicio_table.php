<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_ordenes_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 50)->unique();
            $table->foreignId('st_cliente_id')->constrained('st_clientes')->onDelete('restrict');
            $table->foreignId('st_equipo_id')->nullable()->constrained('st_equipos')->onDelete('restrict');
            $table->foreignId('st_tecnico_id')->nullable()->constrained('st_tecnicos')->onDelete('set null');
            $table->string('tipo_servicio'); // Reparación, Mantenimiento Preventivo, Instalación, Diagnóstico, Garantía
            $table->string('prioridad', 20)->default('media'); // baja, media, alta, urgente
            $table->string('estado', 30)->default('recibida'); // recibida, asignada, en_proceso, pendiente_repuestos, completada, entregada, cancelada
            $table->text('descripcion_problema');
            $table->text('accesorios_entregados')->nullable(); // Cable, fuente, manual, etc.
            $table->date('fecha_recepcion');
            $table->date('fecha_promesa_entrega')->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_inicio_trabajo')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->decimal('costo_mano_obra', 12, 2)->nullable();
            $table->decimal('costo_repuestos', 12, 2)->nullable();
            $table->decimal('costo_total', 12, 2)->nullable();
            $table->boolean('cliente_notificado')->default(false);
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Usuario que registró
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_ordenes_servicio');
    }
};
