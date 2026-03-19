<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAutorizacionPdv extends Model
{
    protected $table = 'log_autorizaciones_pdv';

    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'referencia_tipo',
        'referencia_id',
        'usuario_solicitante_id',
        'usuario_autorizador_id',
        'detalle',
        'created_at',
    ];

    protected $casts = [
        'detalle' => 'json',
        'created_at' => 'datetime',
    ];

    // Relaciones
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    public function autorizador()
    {
        return $this->belongsTo(User::class, 'usuario_autorizador_id');
    }

    // Métodos estáticos
    public static function registrar($tipo, $referenciaTipo, $referenciaId, $solicitanteId, $autorizadorId, $detalle = null)
    {
        return self::create([
            'tipo' => $tipo,
            'referencia_tipo' => $referenciaTipo,
            'referencia_id' => $referenciaId,
            'usuario_solicitante_id' => $solicitanteId,
            'usuario_autorizador_id' => $autorizadorId,
            'detalle' => $detalle,
            'created_at' => now(),
        ]);
    }

    public function getTipoLabelAttribute()
    {
        return match($this->tipo) {
            'descuento' => 'Descuento por línea',
            'precio' => 'Cambio de precio',
            'anulacion' => 'Anulación de venta',
            'vale_anulacion' => 'Anulación de vale',
            'descuento_global' => 'Descuento global',
            default => $this->tipo,
        };
    }
}
