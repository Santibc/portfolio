<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoPrestacion;
use App\Enums\TipoPrestacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrestacionSocial extends Model
{
    use SoftDeletes;

    protected $table = 'prestaciones_sociales';

    protected $fillable = [
        'empleado_id',
        'metodo_pago_id',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'dias',
        'base',
        'valor',
        'intereses',
        'fondo',
        'estado',
        'fecha_pago',
        'observacion',
    ];

    protected $casts = [
        'tipo' => TipoPrestacion::class,
        'estado' => EstadoPrestacion::class,
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_pago' => 'date',
        'dias' => 'integer',
        'base' => 'integer',
        'valor' => 'integer',
        'intereses' => 'integer',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id')->withTrashed();
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', EstadoPrestacion::Pendiente->value);
    }

    public function getValorFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->valor, 0, ',', '.');
    }

    public function getBaseFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->base, 0, ',', '.');
    }

    public function getInteresesFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->intereses, 0, ',', '.');
    }
}
