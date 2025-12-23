<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParteDiarioLinea extends Model
{
    protected $table = 'parte_diario_lineas';

    public $timestamps = false;

    protected $fillable = [
        'parte_diario_id',
        'herbicida',
        'desbroce',
        'poda',
        'tala',
        'limpieza',
        'pk_inicio',
        'pk_fin',
        'margen_izquierda',
        'ancho_izquierda',
        'margen_derecha',
        'ancho_derecha',
        'unidades',
        'metros_cuadrados',
        'observaciones',
        'orden',
    ];

    protected $casts = [
        'herbicida' => 'boolean',
        'desbroce' => 'boolean',
        'poda' => 'boolean',
        'tala' => 'boolean',
        'limpieza' => 'boolean',
        'margen_izquierda' => 'boolean',
        'margen_derecha' => 'boolean',
        'ancho_izquierda' => 'decimal:2',
        'ancho_derecha' => 'decimal:2',
        'metros_cuadrados' => 'decimal:2',
    ];

    public function parteDiario(): BelongsTo
    {
        return $this->belongsTo(ParteDiario::class);
    }

    // Calcular metros cuadrados basado en PKs y anchos
    public function calcularMetrosCuadrados(): float
    {
        // Convertir PKs a metros (formato XXX+YYY)
        $inicio = $this->pkAMetros($this->pk_inicio);
        $fin = $this->pkAMetros($this->pk_fin);

        if ($inicio === null || $fin === null) return 0;

        $longitud = abs($fin - $inicio);
        $anchoTotal = ($this->ancho_izquierda ?? 0) + ($this->ancho_derecha ?? 0);

        return $longitud * $anchoTotal;
    }

    private function pkAMetros(?string $pk): ?float
    {
        if (!$pk) return null;

        // Formato: XXX+YYY (kilómetros + metros)
        if (preg_match('/(\d+)\+(\d+)/', $pk, $matches)) {
            return ($matches[1] * 1000) + $matches[2];
        }

        return null;
    }
}
