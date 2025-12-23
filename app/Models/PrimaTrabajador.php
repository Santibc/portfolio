<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrimaTrabajador extends Model
{
    protected $table = 'primas_trabajador';

    public $timestamps = false;

    protected $fillable = [
        'trabajador_id',
        'obra_id',
        'parte_diario_id',
        'prima_configuracion_id',
        'fecha',
        'produccion_equipo',
        'trabajadores_equipo',
        'minimo_requerido',
        'excedente',
        'tramos_conseguidos',
        'importe_prima',
        'pagada',
        'fecha_pago',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_pago' => 'date',
        'produccion_equipo' => 'decimal:2',
        'minimo_requerido' => 'decimal:2',
        'excedente' => 'decimal:2',
        'importe_prima' => 'decimal:2',
        'pagada' => 'boolean',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function parteDiario(): BelongsTo
    {
        return $this->belongsTo(ParteDiario::class);
    }

    public function configuracion(): BelongsTo
    {
        return $this->belongsTo(PrimaConfiguracion::class, 'prima_configuracion_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('pagada', false);
    }

    public function scopePagadas($query)
    {
        return $query->where('pagada', true);
    }
}
