<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    protected $table = 'empleados';

    protected $fillable = [
        'metodo_pago_id',
        'nombre',
        'documento',
        'cargo',
        'salario_base',
        'auxilio_transporte',
        'tiene_auxilio',
        'bono_default',
        'porcentaje_salud',
        'porcentaje_pension',
        'eps',
        'fondo_pension',
        'fondo_cesantias',
        'fecha_ingreso',
        'banco',
        'numero_cuenta',
        'activo',
    ];

    protected $casts = [
        'salario_base' => 'integer',
        'auxilio_transporte' => 'integer',
        'tiene_auxilio' => 'boolean',
        'bono_default' => 'integer',
        'porcentaje_salud' => 'integer',
        'porcentaje_pension' => 'integer',
        'fecha_ingreso' => 'date',
        'activo' => 'boolean',
    ];

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(NominaDetalle::class);
    }

    public function prestaciones(): HasMany
    {
        return $this->hasMany(PrestacionSocial::class);
    }

    public function pagosAhorroNomina(): HasMany
    {
        return $this->hasMany(PagoAhorroNomina::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /** Ahorro disponible: lo retenido en nóminas menos lo ya entregado. */
    public function getAhorroAcumuladoAttribute(): int
    {
        $aportado = $this->total_ahorrado
            ?? (int) $this->detalles()->sum('ahorro');
        $pagado = $this->total_pagado_ahorro
            ?? (int) $this->pagosAhorroNomina()->sum('monto');

        return (int) $aportado - (int) $pagado;
    }

    public function getSalarioBaseFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->salario_base, 0, ',', '.');
    }

    public function getAuxilioTransporteFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->auxilio_transporte, 0, ',', '.');
    }

    public function getBonoDefaultFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->bono_default, 0, ',', '.');
    }

    public function getAhorroAcumuladoFormateadoAttribute(): string
    {
        return '$ '.number_format($this->ahorro_acumulado, 0, ',', '.');
    }
}
