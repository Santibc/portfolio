<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoMercadoItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MercadoItem extends Model
{
    use HasFactory;

    protected $table = 'mercado_items';

    protected $fillable = [
        'mercado_id',
        'lista_mercado_item_id',
        'producto_mercado_id',
        'tipo_producto_mercado_id',
        'cantidad_sugerida',
        'estado',
        'registro_mercado_id',
    ];

    protected $casts = [
        'estado'            => EstadoMercadoItem::class,
        'cantidad_sugerida' => 'integer',
    ];

    public function mercado(): BelongsTo
    {
        return $this->belongsTo(Mercado::class, 'mercado_id');
    }

    public function listaItem(): BelongsTo
    {
        return $this->belongsTo(ListaMercadoItem::class, 'lista_mercado_item_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(ProductoMercado::class, 'producto_mercado_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoProductoMercado::class, 'tipo_producto_mercado_id');
    }

    public function registro(): BelongsTo
    {
        return $this->belongsTo(RegistroMercado::class, 'registro_mercado_id');
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', EstadoMercadoItem::Pendiente->value);
    }

    public function scopeRegistrados(Builder $query): Builder
    {
        return $query->where('estado', EstadoMercadoItem::Registrado->value);
    }

    public function scopeSaltados(Builder $query): Builder
    {
        return $query->where('estado', EstadoMercadoItem::Saltado->value);
    }
}
