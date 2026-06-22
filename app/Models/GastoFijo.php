<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GastoFijo extends Model
{
    use SoftDeletes;

    protected $table = 'gastos_fijos';

    protected $fillable = [
        'concepto_gasto_fijo_id',
        'metodo_pago_id',
        'user_id',
        'valor',
        'fecha',
        'observacion',
    ];

    protected $casts = [
        'valor' => 'integer',
        'fecha' => 'date',
    ];

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoGastoFijo::class, 'concepto_gasto_fijo_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getValorFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->valor, 0, ',', '.');
    }

    public function scopeEnRangoFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('fecha', [
            Carbon::parse($desde)->startOfDay(),
            Carbon::parse($hasta)->endOfDay(),
        ]);
    }
}
