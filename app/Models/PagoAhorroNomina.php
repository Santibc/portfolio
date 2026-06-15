<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoAhorroNomina extends Model
{
    use SoftDeletes;

    protected $table = 'pagos_ahorro_nomina';

    protected $fillable = [
        'empleado_id',
        'user_id',
        'monto',
        'observacion',
        'pagado_en',
    ];

    protected $casts = [
        'monto' => 'integer',
        'pagado_en' => 'date',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
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
