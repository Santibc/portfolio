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
    ];

    protected $casts = [
        'cantidad' => 'integer',
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

    public function mercadoItem(): HasOne
    {
        return $this->hasOne(MercadoItem::class, 'registro_mercado_id');
    }

    public function getValorFormateadoAttribute(): string
    {
        return '$ ' . number_format((int) $this->valor, 0, ',', '.');
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
