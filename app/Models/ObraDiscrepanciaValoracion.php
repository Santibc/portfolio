<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObraDiscrepanciaValoracion extends Model
{
    use HasFactory;

    protected $table = 'obra_discrepancias_valoracion';

    protected $fillable = [
        'obra_id',
        'periodo_mes',
        'importe_producido_manzer',
        'importe_validado_cuadrilla',
        'importe_aceptado_cliente',
        'fecha_respuesta_cliente',
        'importe_pendiente',
        'estado',
        'notas',
        'documento_valoracion_path',
        'registrado_por',
        'fecha_resolucion',
    ];

    protected $casts = [
        'importe_producido_manzer' => 'decimal:2',
        'importe_validado_cuadrilla' => 'decimal:2',
        'importe_aceptado_cliente' => 'decimal:2',
        'importe_pendiente' => 'decimal:2',
        'fecha_respuesta_cliente' => 'date',
        'fecha_resolucion' => 'date',
    ];

    // Relaciones
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    public function scopePorPeriodo($query, $periodo)
    {
        return $query->where('periodo_mes', $periodo);
    }

    // Mutators
    public function setPeriodoMesAttribute($value)
    {
        // Asegurar formato YYYY-MM
        if (strlen($value) === 7 && strpos($value, '-') === 4) {
            $this->attributes['periodo_mes'] = $value;
        } else {
            // Intentar convertir desde fecha
            $this->attributes['periodo_mes'] = date('Y-m', strtotime($value));
        }
    }

    // Accessors
    public function getImporteProducidoFormateadoAttribute(): string
    {
        return number_format($this->importe_producido_manzer, 2, ',', '.') . ' €';
    }

    public function getImporteAceptadoFormateadoAttribute(): string
    {
        return $this->importe_aceptado_cliente
            ? number_format($this->importe_aceptado_cliente, 2, ',', '.') . ' €'
            : '-';
    }

    public function getImportePendienteFormateadoAttribute(): string
    {
        return number_format($this->importe_pendiente, 2, ',', '.') . ' €';
    }

    public function getPeriodoFormateadoAttribute(): string
    {
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        list($year, $month) = explode('-', $this->periodo_mes);
        return $meses[$month] . ' ' . $year;
    }

    // Métodos helper
    public function marcarResuelto(): bool
    {
        $this->estado = 'resuelto';
        $this->fecha_resolucion = now();
        return $this->save();
    }

    public function calcularPendiente(): void
    {
        $aceptado = $this->importe_aceptado_cliente ?? 0;
        $this->importe_pendiente = $this->importe_producido_manzer - $aceptado;
    }
}
