<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RegistroMercado extends Model
{
    use HasFactory;

    protected $table = 'registros_mercado';

    protected $fillable = [
        'producto_mercado_id',
        'mercado_id',
        'cantidad',
        'valor',
        'metodo_pago_id',
        'turno_caja_id',
        'observacion',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'valor'    => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(ProductoMercado::class, 'producto_mercado_id');
    }

    public function mercado(): BelongsTo
    {
        return $this->belongsTo(Mercado::class, 'mercado_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(TurnoCaja::class, 'turno_caja_id');
    }

    public function mercadoItem(): HasOne
    {
        return $this->hasOne(MercadoItem::class, 'registro_mercado_id');
    }

    public function getValorFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->valor, 0, ',', '.');
    }

    public function getCantidadFormateadaAttribute(): string
    {
        $cantidad = (float) $this->cantidad;

        // Sin decimales si es entero; con hasta 2 decimales (sin ceros de relleno) si no.
        if (floor($cantidad) === $cantidad) {
            return number_format($cantidad, 0, ',', '.');
        }

        return rtrim(rtrim(number_format($cantidad, 2, ',', '.'), '0'), ',');
    }

    public function scopeDeHoy(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeEnRangoFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('created_at', [
            Carbon::parse($desde)->startOfDay(),
            Carbon::parse($hasta)->endOfDay(),
        ]);
    }
}
