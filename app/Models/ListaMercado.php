<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ListaMercado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'listas_mercado';

    protected $fillable = [
        'nombre',
        'slug',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ListaMercadoItem::class, 'lista_id');
    }

    public function mercados(): HasMany
    {
        return $this->hasMany(Mercado::class, 'lista_id');
    }

    public function scopeActiva(Builder $query): Builder
    {
        return $query->where('activa', true);
    }

    public static function actual(): self
    {
        return self::activa()->firstOr(function () {
            return self::create([
                'nombre' => 'Lista semanal',
                'slug'   => Str::slug('lista-semanal-' . now()->timestamp),
                'activa' => true,
            ]);
        });
    }
}
