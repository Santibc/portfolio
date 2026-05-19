<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoMercado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mercado extends Model
{
    use HasFactory;

    protected $table = 'mercados';

    protected $fillable = [
        'lista_id',
        'user_id',
        'estado',
        'iniciado_en',
        'finalizado_en',
    ];

    protected $casts = [
        'estado'        => EstadoMercado::class,
        'iniciado_en'   => 'datetime',
        'finalizado_en' => 'datetime',
    ];

    public function lista(): BelongsTo
    {
        return $this->belongsTo(ListaMercado::class, 'lista_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MercadoItem::class, 'mercado_id');
    }

    public function registros(): HasMany
    {
        return $this->hasMany(RegistroMercado::class, 'mercado_id');
    }

    public function scopeEnProgreso(Builder $query): Builder
    {
        return $query->where('estado', EstadoMercado::EnProgreso->value);
    }

    public function scopeCompletados(Builder $query): Builder
    {
        return $query->where('estado', EstadoMercado::Completado->value);
    }

    public function getTotalGastadoAttribute(): int
    {
        return (int) $this->registros()->sum('valor');
    }
}
