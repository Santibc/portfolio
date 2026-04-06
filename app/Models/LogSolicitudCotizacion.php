<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSolicitudCotizacion extends Model
{
    protected $table = 'log_solicitudes_cotizacion';
    public $timestamps = false;

    protected $fillable = [
        'solicitud_cotizacion_id',
        'usuario_id',
        'accion',
        'detalle',
        'created_at',
    ];

    protected $casts = [
        'detalle' => 'json',
        'created_at' => 'datetime',
    ];

    const ACCION_CREACION = 'creacion';
    const ACCION_EDICION = 'edicion';
    const ACCION_CAMBIO_CLIENTE = 'cambio_cliente';

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCotizacion::class, 'solicitud_cotizacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(int $solicitudId, string $accion, ?array $detalle = null): self
    {
        return self::create([
            'solicitud_cotizacion_id' => $solicitudId,
            'usuario_id' => auth()->id(),
            'accion' => $accion,
            'detalle' => $detalle,
            'created_at' => now(),
        ]);
    }

    public function getAccionLabelAttribute(): string
    {
        return match ($this->accion) {
            'creacion' => 'Creación',
            'edicion' => 'Edición',
            'cambio_cliente' => 'Cambio de Cliente',
            default => ucfirst($this->accion),
        };
    }

    public function getAccionColorAttribute(): string
    {
        return match ($this->accion) {
            'creacion' => 'primary',
            'edicion' => 'warning',
            'cambio_cliente' => 'info',
            default => 'secondary',
        };
    }

    public function getAccionIconAttribute(): string
    {
        return match ($this->accion) {
            'creacion' => 'bi-plus-circle',
            'edicion' => 'bi-pencil',
            'cambio_cliente' => 'bi-person-gear',
            default => 'bi-clock',
        };
    }
}
