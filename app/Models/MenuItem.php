<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $table = 'menu_items';

    protected $fillable = [
        'nombre',
        'precio',
        'imagen',
        'tipo_id',
        'activo',
        'orden',
    ];

    protected $casts = [
        'precio' => 'integer',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoMenuItem::class, 'tipo_id');
    }

    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function dias(): BelongsToMany
    {
        return $this->belongsToMany(DiaSemana::class, 'menu_dia', 'menu_item_id', 'dia_semana_id')
            ->withTimestamps();
    }

    public function getImagenUrlAttribute(): string
    {
        return $this->imagen ? asset('uploads/menu-items/'.$this->imagen) : '';
    }

    public function getPrecioFormateadoAttribute(): string
    {
        return '$ '.number_format((int) $this->precio, 0, ',', '.');
    }

    public function hasImagen(): bool
    {
        return ! empty($this->imagen);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeDelTipo(Builder $query, int $tipoId): Builder
    {
        return $query->where('tipo_id', $tipoId);
    }

    /** Items configurados para un día de la semana (ISO: 1=Lunes … 7=Domingo). */
    public function scopeParaDia(Builder $query, int $diaIso): Builder
    {
        return $query->whereHas('dias', fn (Builder $q) => $q->whereKey($diaIso));
    }
}
