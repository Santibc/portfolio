<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoTurno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TurnoCaja extends Model
{
    protected $table = 'turnos_caja';

    protected $fillable = [
        'user_apertura_id',
        'user_cierre_id',
        'abierto_en',
        'base_inicial',
        'cerrado_en',
        'total_declarado',
        'notas',
    ];

    protected $casts = [
        'abierto_en'      => 'datetime',
        'cerrado_en'      => 'datetime',
        'base_inicial'    => 'integer',
        'total_declarado' => 'integer',
    ];

    public function aperturadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_apertura_id');
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cierre_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function scopeAbierto(Builder $query): Builder
    {
        return $query->whereNull('cerrado_en');
    }

    public function scopeCerrados(Builder $query): Builder
    {
        return $query->whereNotNull('cerrado_en');
    }

    public function getEstadoAttribute(): string
    {
        return $this->cerrado_en === null ? EstadoTurno::Abierto->value : EstadoTurno::Cerrado->value;
    }

    public function getEstaAbiertoAttribute(): bool
    {
        return $this->cerrado_en === null;
    }

    public function getTotalVentasAttribute(): int
    {
        return (int) $this->ventas->sum('total');
    }

    public function getTotalEfectivoAttribute(): int
    {
        // Efectivo NETO que queda en el cajón: lo recibido en efectivo menos el
        // cambio devuelto en cada venta. El cambio nunca entra en los totales.
        return (int) $this->ventas->sum(function ($venta) {
            $efectivo = $venta->pagos
                ->filter(fn ($p) => optional($p->metodo)->es_efectivo)
                ->sum('monto');

            return max(0, (int) $efectivo - (int) $venta->cambio);
        });
    }

    public function getTotalNoEfectivoAttribute(): int
    {
        return (int) $this->ventas
            ->flatMap->pagos
            ->filter(fn ($p) => ! optional($p->metodo)->es_efectivo)
            ->sum('monto');
    }

    public function getTotalCambioAttribute(): int
    {
        return (int) $this->ventas->sum('cambio');
    }

    public function getTotalGastosAttribute(): int
    {
        return (int) $this->gastos->sum('valor');
    }

    public function getTotalAhorrosAttribute(): int
    {
        return (int) $this->gastos->sum('ahorro');
    }

    public function getNetoAttribute(): int
    {
        return $this->total_ventas - $this->total_gastos - $this->total_ahorros;
    }

    public function getEfectivoEsperadoAttribute(): int
    {
        // total_efectivo ya viene neto del cambio; el cambio no se resta aquí.
        return (int) $this->base_inicial + $this->total_efectivo - $this->total_gastos - $this->total_ahorros;
    }

    public function getDiferenciaCierreAttribute(): ?int
    {
        if ($this->cerrado_en === null || $this->total_declarado === null) {
            return null;
        }

        return (int) $this->total_declarado - $this->efectivo_esperado;
    }

    public function getBaseInicialFormateadaAttribute(): string
    {
        return '$ ' . number_format((int) $this->base_inicial, 0, ',', '.');
    }

    public function getTotalVentasFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->total_ventas, 0, ',', '.');
    }

    public function getTotalEfectivoFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->total_efectivo, 0, ',', '.');
    }

    public function getEfectivoEsperadoFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->efectivo_esperado, 0, ',', '.');
    }

    public function getTotalGastosFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->total_gastos, 0, ',', '.');
    }

    public function getTotalAhorrosFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->total_ahorros, 0, ',', '.');
    }

    public function getNetoFormateadoAttribute(): string
    {
        $n     = $this->neto;
        $signo = $n < 0 ? '-' : '';
        return $signo . '$ ' . number_format(abs($n), 0, ',', '.');
    }

    public function getTotalDeclaradoFormateadoAttribute(): string
    {
        return $this->total_declarado !== null
            ? '$ ' . number_format((int) $this->total_declarado, 0, ',', '.')
            : '—';
    }

    public function getDiferenciaCierreFormateadaAttribute(): string
    {
        $d = $this->diferencia_cierre;
        if ($d === null) {
            return '—';
        }
        $signo = $d > 0 ? '+' : ($d < 0 ? '-' : '');

        return $signo . '$ ' . number_format(abs($d), 0, ',', '.');
    }
}
