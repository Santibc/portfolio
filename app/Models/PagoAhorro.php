<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoAhorro extends Model
{
    use SoftDeletes;

    protected $table = 'pagos_ahorro';

    protected $fillable = [
        'trabajador_turno_id',
        'user_id',
        'monto',
        'observacion',
        'pagado_en',
    ];

    protected $casts = [
        'monto' => 'integer',
        'pagado_en' => 'datetime',
    ];

    public function trabajadorTurno(): BelongsTo
    {
        return $this->belongsTo(TrabajadorTurno::class, 'trabajador_turno_id');
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
