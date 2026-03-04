<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cumpleanos_configuracion', function (Blueprint $table) {
            $table->id();
            $table->boolean('activa')->default(false)->comment('Activar/desactivar envio de emails de cumpleanos');
            $table->string('asunto')->default('¡Feliz Cumpleaños, {nombre}!')->comment('Asunto del email, soporta placeholders');
            $table->text('cuerpo')->comment('Cuerpo HTML del email, soporta placeholders');
            $table->string('adjunto_path')->nullable()->comment('Ruta al archivo adjunto');
            $table->string('adjunto_nombre_original')->nullable()->comment('Nombre original del archivo adjunto');
            $table->string('hora_envio', 5)->default('08:00')->comment('Hora del dia para enviar HH:MM');
            $table->timestamps();
        });

        // Insertar configuración por defecto
        DB::table('cumpleanos_configuracion')->insert([
            'activa' => false,
            'asunto' => '¡Feliz Cumpleaños, {nombre}!',
            'cuerpo' => '<p>Querido/a <strong>{nombre_completo}</strong>,</p>'
                      . '<p>Desde Manzer Agroforestal queremos desearte un muy feliz cumpleaños. 🎂</p>'
                      . '<p>Esperamos que pases un día estupendo rodeado/a de los tuyos.</p>'
                      . '<p>¡Un fuerte abrazo de todo el equipo!</p>'
                      . '<p><strong>Manzer Agroforestal, S.R.L.U.</strong></p>',
            'hora_envio' => '08:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('cumpleanos_configuracion');
    }
};
