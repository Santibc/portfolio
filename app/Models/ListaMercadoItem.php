<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListaMercadoItem extends Model
{
    use HasFactory;

    protected $table = 'lista_mercado_items';

    protected $fillable = [
        'lista_id',
        'producto_mercado_id',
        'cantidad_sugerida',
        'orden',
    ];

    protected $casts = [
        'cantidad_sugerida' => 'integer',
        'orden'             => 'integer',
    ];

    public function lista(): BelongsTo
    {
        return $this->belongsTo(ListaMercado::class, 'lista_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(ProductoMercado::class, 'producto_mercado_id');
    }
}
