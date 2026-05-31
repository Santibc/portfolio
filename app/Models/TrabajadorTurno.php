<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrabajadorTurno extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores_turno';

    protected $fillable = [
        'nombre',
        'valor_turno_default',
        'valor_ahorro_default',
        'activo',
    ];

    protected $casts = [
        'valor_turno_default' => 'integer',
        'valor_ahorro_default' => 'integer',
        'activo' => 'boolean',
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function pagosAhorro(): HasMany
    {
        return $this->hasMany(PagoAhorro::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function getValorTurnoDefaultFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->valor_turno_default, 0, ',', '.');
    }

    public function getValorAhorroDefaultFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->valor_ahorro_default, 0, ',', '.');
    }

    /**
     * Ahorro acumulado = Σ aportes (gastos.ahorro) − Σ pagos de ahorro.
     * Usa los agregados inyectados por withSum() si están disponibles para evitar N+1;
     * de lo contrario los calcula con sub-queries.
     */
    public function getAhorroAcumuladoAttribute(): int
    {
        $aportado = $this->total_ahorrado !== null
            ? (int) $this->total_ahorrado
            : (int) $this->gastos()->sum('ahorro');

        $pagado = $this->total_pagado_ahorro !== null
            ? (int) $this->total_pagado_ahorro
            : (int) $this->pagosAhorro()->sum('monto');

        return $aportado - $pagado;
    }

    public function getAhorroAcumuladoFormateadoAttribute(): string
    {
        return '$ '.number_format($this->ahorro_acumulado, 0, ',', '.');
    }
}
