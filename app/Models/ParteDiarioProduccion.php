<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParteDiarioProduccion extends Model
{
    use HasFactory;

    protected $table = 'parte_diario_producciones';

    protected $fillable = [
        'parte_diario_id',
        'concepto_produccion_id',
        'cantidad',
        'precio_unitario',
        'importe_calculado',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'importe_calculado' => 'decimal:2',
    ];

    // Relaciones
    public function parteDiario(): BelongsTo
    {
        return $this->belongsTo(ParteDiario::class);
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ObraConceptoProduccion::class, 'concepto_produccion_id');
    }

    // Events (Boot)
    protected static function boot()
    {
        parent::boot();

        // Calcular importe automáticamente al crear/actualizar
        static::saving(function ($produccion) {
            $produccion->importe_calculado = $produccion->cantidad * $produccion->precio_unitario;
        });

        // Después de guardar, actualizar el total del parte diario
        static::saved(function ($produccion) {
            $produccion->parteDiario->calcularYActualizarImporte();
        });

        // Después de eliminar, actualizar el total del parte diario
        static::deleted(function ($produccion) {
            if ($produccion->parteDiario) {
                $produccion->parteDiario->calcularYActualizarImporte();
            }
        });
    }

    // Accessors
    public function getImporteFormateadoAttribute(): string
    {
        return number_format($this->importe_calculado, 2, ',', '.') . ' €';
    }

    public function getCantidadFormateadaAttribute(): string
    {
        return number_format($this->cantidad, 2, ',', '.');
    }
}
