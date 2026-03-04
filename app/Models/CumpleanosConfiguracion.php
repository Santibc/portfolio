<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CumpleanosConfiguracion extends Model
{
    protected $table = 'cumpleanos_configuracion';

    protected $fillable = [
        'activa',
        'asunto',
        'cuerpo',
        'adjunto_path',
        'adjunto_nombre_original',
        'hora_envio',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * Obtener la configuracion (singleton - siempre una sola fila)
     */
    public static function obtener(): self
    {
        return self::firstOrCreate([], [
            'activa' => false,
            'asunto' => '¡Feliz Cumpleaños, {nombre}!',
            'cuerpo' => self::plantillaDefault(),
            'hora_envio' => '08:00',
        ]);
    }

    /**
     * Reemplazar placeholders en el asunto
     */
    public function resolverAsunto(Trabajador $trabajador): string
    {
        return $this->reemplazarPlaceholders($this->asunto, $trabajador);
    }

    /**
     * Reemplazar placeholders en el cuerpo
     */
    public function resolverCuerpo(Trabajador $trabajador): string
    {
        return $this->reemplazarPlaceholders($this->cuerpo, $trabajador);
    }

    /**
     * Reemplazar placeholders con datos del trabajador
     */
    protected function reemplazarPlaceholders(string $texto, Trabajador $trabajador): string
    {
        return str_replace(
            ['{nombre}', '{apellidos}', '{nombre_completo}'],
            [$trabajador->nombre, $trabajador->apellidos, $trabajador->nombre_completo],
            $texto
        );
    }

    /**
     * Plantilla HTML por defecto
     */
    public static function plantillaDefault(): string
    {
        return '<p>Querido/a <strong>{nombre_completo}</strong>,</p>'
             . '<p>Desde Manzer Agroforestal queremos desearte un muy feliz cumpleaños. 🎂</p>'
             . '<p>Esperamos que pases un día estupendo rodeado/a de los tuyos.</p>'
             . '<p>¡Un fuerte abrazo de todo el equipo!</p>'
             . '<p><strong>Manzer Agroforestal, S.R.L.U.</strong></p>';
    }
}
