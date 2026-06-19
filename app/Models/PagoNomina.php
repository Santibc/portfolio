<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoNomina extends Model
{
    use SoftDeletes;

    protected $table = 'pagos_nomina';

    protected $fillable = [
        'nomina_detalle_id',
        'metodo_pago_id',
        'user_id',
        'monto',
        'referencia',
        'fecha_pago',
    ];

    protected $casts = [
        'monto' => 'integer',
        'fecha_pago' => 'date',
    ];

    public function detalle(): BelongsTo
    {
        return $this->belongsTo(NominaDetalle::class, 'nomina_detalle_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMontoFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->monto, 0, ',', '.');
    }
}
