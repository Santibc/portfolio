<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipoGasto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use SoftDeletes;

    protected $table = 'gastos';

    protected $fillable = [
        'turno_caja_id',
        'user_id',
        'tipo',
        'trabajador_turno_id',
        'metodo_pago_id',
        'valor',
        'ahorro',
        'observacion',
    ];

    protected $casts = [
        'tipo'   => TipoGasto::class,
        'valor'  => 'integer',
        'ahorro' => 'integer',
    ];

    public function turno(): BelongsTo
    {
        return $this->belongsTo(TurnoCaja::class, 'turno_caja_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trabajadorTurno(): BelongsTo
    {
        return $this->belongsTo(TrabajadorTurno::class, 'trabajador_turno_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function getValorFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->valor, 0, ',', '.');
    }

    public function getAhorroFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->ahorro, 0, ',', '.');
    }
}
