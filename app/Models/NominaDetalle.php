<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoPagoNomina;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class NominaDetalle extends Model
{
    use SoftDeletes;

    protected $table = 'nomina_detalles';

    protected $fillable = [
        'nomina_id',
        'empleado_id',
        'empleado_nombre',
        'dias',
        'salario_base',
        'auxilio',
        'bono',
        'porcentaje_salud',
        'porcentaje_pension',
        'basico',
        'salud',
        'pension',
        'total_devengado',
        'total_deducido',
        'neto',
        'ahorro',
        'observacion',
    ];

    protected $casts = [
        'dias' => 'integer',
        'salario_base' => 'integer',
        'auxilio' => 'integer',
        'bono' => 'integer',
        'porcentaje_salud' => 'integer',
        'porcentaje_pension' => 'integer',
        'basico' => 'integer',
        'salud' => 'integer',
        'pension' => 'integer',
        'total_devengado' => 'integer',
        'total_deducido' => 'integer',
        'neto' => 'integer',
        'ahorro' => 'integer',
    ];

    public function nomina(): BelongsTo
    {
        return $this->belongsTo(Nomina::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoNomina::class);
    }

    public function getTotalPagadoAttribute(): int
    {
        // Usa el withSum si está cargado; si no, suma la relación.
        return (int) ($this->pagos_sum_monto
            ?? $this->pagos->sum('monto'));
    }

    public function getSaldoPendienteAttribute(): int
    {
        return max(0, (int) $this->neto - $this->total_pagado);
    }

    public function getEstadoPagoAttribute(): EstadoPagoNomina
    {
        $pagado = $this->total_pagado;

        return match (true) {
            $pagado <= 0 => EstadoPagoNomina::Pendiente,
            $pagado >= (int) $this->neto => EstadoPagoNomina::Pagado,
            default => EstadoPagoNomina::Parcial,
        };
    }

    public function getBasicoFormateadoAttribute(): string
    {
        return $this->fmt($this->basico);
    }

    public function getAuxilioFormateadoAttribute(): string
    {
        return $this->fmt($this->auxilio);
    }

    public function getBonoFormateadoAttribute(): string
    {
        return $this->fmt($this->bono);
    }

    public function getSaludFormateadoAttribute(): string
    {
        return $this->fmt($this->salud);
    }

    public function getPensionFormateadoAttribute(): string
    {
        return $this->fmt($this->pension);
    }

    public function getTotalDevengadoFormateadoAttribute(): string
    {
        return $this->fmt($this->total_devengado);
    }

    public function getTotalDeducidoFormateadoAttribute(): string
    {
        return $this->fmt($this->total_deducido);
    }

    public function getNetoFormateadoAttribute(): string
    {
        return $this->fmt($this->neto);
    }

    public function getAhorroFormateadoAttribute(): string
    {
        return $this->fmt($this->ahorro);
    }

    public function getSalarioBaseFormateadoAttribute(): string
    {
        return $this->fmt($this->salario_base);
    }

    public function getTotalPagadoFormateadoAttribute(): string
    {
        return $this->fmt($this->total_pagado);
    }

    public function getSaldoPendienteFormateadoAttribute(): string
    {
        return $this->fmt($this->saldo_pendiente);
    }

    private function fmt(int $valor): string
    {
        return '$ '.number_format($valor, 0, ',', '.');
    }
}
