<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'turno_caja_id',
        'user_id',
        'total',
        'efectivo_recibido',
        'cambio',
        'notas',
    ];

    protected $casts = [
        'total'             => 'integer',
        'efectivo_recibido' => 'integer',
        'cambio'            => 'integer',
    ];

    public function turno(): BelongsTo
    {
        return $this->belongsTo(TurnoCaja::class, 'turno_caja_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(VentaPago::class);
    }

    public function getTotalFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->total, 0, ',', '.');
    }

    public function getCambioFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->cambio, 0, ',', '.');
    }

    public function getEfectivoRecibidoFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->efectivo_recibido, 0, ',', '.');
    }
}
