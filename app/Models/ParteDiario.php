<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParteDiario extends Model
{
    protected $table = 'partes_diarios';

    protected $fillable = [
        'obra_id',
        'fecha',
        'tipo',
        'fecha_fin',
        'jornada',
        'linea',
        'trayecto',
        'gerencia_jefatura',
        'distrito',
        'brigada',
        'desbroce_total_m2',
        'desbroce_p5_m2',
        'desbroce_p6_m2',
        'limpieza_p8_m2',
        'herbicida_p4_m2',
        'talas_unidades',
        'podas_unidades',
        'observaciones',
        'incidencias',
        'encargado_firma',
        'encargado_nombre',
        'cliente_firma',
        'cliente_nombre',
        'estado',
        'creado_por',
        'importe_total_calculado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_fin' => 'date',
        'desbroce_total_m2' => 'decimal:2',
        'desbroce_p5_m2' => 'decimal:2',
        'desbroce_p6_m2' => 'decimal:2',
        'limpieza_p8_m2' => 'decimal:2',
        'herbicida_p4_m2' => 'decimal:2',
        'importe_total_calculado' => 'decimal:2',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function trabajadores(): HasMany
    {
        return $this->hasMany(ParteDiarioTrabajador::class);
    }

    public function lineas(): HasMany
    {
        return $this->hasMany(ParteDiarioLinea::class)->orderBy('orden');
    }

    public function herbicidas(): HasMany
    {
        return $this->hasMany(ParteDiarioHerbicida::class);
    }

    public function primas(): HasMany
    {
        return $this->hasMany(PrimaTrabajador::class);
    }

    public function producciones(): HasMany
    {
        return $this->hasMany(ParteDiarioProduccion::class)->with('concepto');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ParteDiarioDocumento::class);
    }

    // Accessors
    public function getImporteTotalAttribute()
    {
        return $this->producciones->sum('importe_calculado');
    }

    public function getImporteTotalFormateadoAttribute(): string
    {
        return number_format($this->importe_total_calculado, 2, ',', '.') . ' €';
    }

    /**
     * Obtener producción agrupada por categoría de concepto.
     */
    public function getProduccionPorCategoriaAttribute(): array
    {
        $categorias = [
            'desbroce' => 0,
            'limpieza' => 0,
            'herbicida' => 0,
            'tala' => 0,
            'poda' => 0,
            'otro' => 0,
        ];

        foreach ($this->producciones as $produccion) {
            if ($produccion->concepto) {
                $cat = $produccion->concepto->categoria ?? 'otro';
                if (isset($categorias[$cat])) {
                    $categorias[$cat] += $produccion->cantidad;
                }
            }
        }

        return $categorias;
    }

    // Métodos helper
    public function calcularYActualizarImporte(): float
    {
        $total = $this->producciones()->sum('importe_calculado');
        $this->update(['importe_total_calculado' => $total]);
        return $total;
    }

    // Scopes
    public function scopeBorradores($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeValidados($query)
    {
        return $query->where('estado', 'validado');
    }

    public function scopeDiarios($query)
    {
        return $query->where('tipo', 'diario');
    }

    public function scopeMensuales($query)
    {
        return $query->where('tipo', 'mensual');
    }

    /**
     * Filtro unificado: partes cuyo rango de fecha solapa con el periodo dado.
     * Diarios: fecha BETWEEN $desde AND $hasta
     * Mensuales: fecha <= $hasta AND fecha_fin >= $desde (solapamiento de rangos)
     */
    public function scopeEnPeriodo($query, $fechaDesde, $fechaHasta)
    {
        return $query->where(function ($q) use ($fechaDesde, $fechaHasta) {
            $q->where(function ($q2) use ($fechaDesde, $fechaHasta) {
                $q2->where('tipo', 'diario')
                   ->whereBetween('fecha', [$fechaDesde, $fechaHasta]);
            })->orWhere(function ($q2) use ($fechaDesde, $fechaHasta) {
                $q2->where('tipo', 'mensual')
                   ->where('fecha', '<=', $fechaHasta)
                   ->where('fecha_fin', '>=', $fechaDesde);
            });
        });
    }

    /**
     * Partes que pertenecen a un mes específico (YYYY-MM).
     */
    public function scopeDelMes($query, int $year, int $month)
    {
        $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return $query->enPeriodo($start, $end);
    }

    // Accessors de tipo
    public function getEsMensualAttribute(): bool
    {
        return $this->tipo === 'mensual';
    }

    public function getEsDiarioAttribute(): bool
    {
        return $this->tipo === 'diario';
    }

    public function getFechaDisplayAttribute(): string
    {
        if ($this->es_mensual && $this->fecha_fin) {
            return $this->fecha->format('d/m') . ' - ' . $this->fecha_fin->format('d/m/Y');
        }
        return $this->fecha->format('d/m/Y');
    }

    public function getPeriodoFormateadoAttribute(): string
    {
        if ($this->es_mensual && $this->fecha_fin) {
            if ($this->fecha->month === $this->fecha_fin->month && $this->fecha->year === $this->fecha_fin->year) {
                return $this->fecha->translatedFormat('F Y');
            }
            return $this->fecha->format('d/m/Y') . ' - ' . $this->fecha_fin->format('d/m/Y');
        }
        return $this->fecha->format('d/m/Y');
    }
}
