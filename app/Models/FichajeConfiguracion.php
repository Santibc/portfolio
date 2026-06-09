<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichajeConfiguracion extends Model
{
    protected $table = 'fichaje_configuracion';

    protected $fillable = [
        'activo',
        'hora_entrada',
        'hora_salida',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Obtener la configuración (singleton — siempre una sola fila).
     */
    public static function obtener(): self
    {
        return self::firstOrCreate([], [
            'activo' => false,
            'hora_entrada' => '08:00',
            'hora_salida' => '17:00',
        ]);
    }

    /**
     * Devuelve la hora en formato H:i (para el scheduler).
     */
    public function horaEntradaCorta(): string
    {
        return substr((string) $this->hora_entrada, 0, 5);
    }

    public function horaSalidaCorta(): string
    {
        return substr((string) $this->hora_salida, 0, 5);
    }
}
