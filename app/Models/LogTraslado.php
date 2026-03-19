<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogTraslado extends Model
{
    protected $table = 'log_traslados';
    public $timestamps = false;

    protected $fillable = [
        'traslado_stock_id',
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
    const ACCION_ENVIO = 'envio';
    const ACCION_RECEPCION = 'recepcion';
    const ACCION_CANCELACION = 'cancelacion';

    public function traslado()
    {
        return $this->belongsTo(TrasladoStock::class, 'traslado_stock_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(int $trasladoId, string $accion, ?array $detalle = null): self
    {
        return self::create([
            'traslado_stock_id' => $trasladoId,
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
            'envio' => 'Envío',
            'recepcion' => 'Recepción',
            'cancelacion' => 'Cancelación',
            default => ucfirst($this->accion),
        };
    }

    public function getAccionColorAttribute(): string
    {
        return match ($this->accion) {
            'creacion' => 'primary',
            'edicion' => 'warning',
            'envio' => 'info',
            'recepcion' => 'success',
            'cancelacion' => 'danger',
            default => 'secondary',
        };
    }

    public function getAccionIconAttribute(): string
    {
        return match ($this->accion) {
            'creacion' => 'bi-plus-circle',
            'edicion' => 'bi-pencil',
            'envio' => 'bi-send',
            'recepcion' => 'bi-check-circle',
            'cancelacion' => 'bi-x-circle',
            default => 'bi-clock',
        };
    }
}
