<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoNomina;
use App\Enums\TipoPeriodo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nomina extends Model
{
    use SoftDeletes;

    protected $table = 'nominas';

    protected $fillable = [
        'creada_por',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'dias',
        'estado',
    ];

    protected $casts = [
        'tipo' => TipoPeriodo::class,
        'estado' => EstadoNomina::class,
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dias' => 'integer',
    ];

    public function creadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creada_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(NominaDetalle::class);
    }

    public function scopeBorrador(Builder $query): Builder
    {
        return $query->where('estado', EstadoNomina::Borrador->value);
    }

    public function getTotalDevengadoAttribute(): int
    {
        return (int) $this->detalles->sum('total_devengado');
    }

    public function getTotalDeducidoAttribute(): int
    {
        return (int) $this->detalles->sum('total_deducido');
    }

    public function getTotalNetoAttribute(): int
    {
        return (int) $this->detalles->sum('neto');
    }

    public function getTotalAhorroAttribute(): int
    {
        return (int) $this->detalles->sum('ahorro');
    }

    public function getTotalPagadoAttribute(): int
    {
        return (int) $this->detalles->sum(fn (NominaDetalle $d) => $d->total_pagado);
    }

    public function getTotalPendienteAttribute(): int
    {
        return max(0, $this->total_neto - $this->total_pagado);
    }

    public function getTotalDevengadoFormateadoAttribute(): string
    {
        return '$ '.number_format($this->total_devengado, 0, ',', '.');
    }

    public function getTotalDeducidoFormateadoAttribute(): string
    {
        return '$ '.number_format($this->total_deducido, 0, ',', '.');
    }

    public function getTotalNetoFormateadoAttribute(): string
    {
        return '$ '.number_format($this->total_neto, 0, ',', '.');
    }

    public function getTotalAhorroFormateadoAttribute(): string
    {
        return '$ '.number_format($this->total_ahorro, 0, ',', '.');
    }

    public function getTotalPagadoFormateadoAttribute(): string
    {
        return '$ '.number_format($this->total_pagado, 0, ',', '.');
    }

    public function getTotalPendienteFormateadoAttribute(): string
    {
        return '$ '.number_format($this->total_pendiente, 0, ',', '.');
    }
}
