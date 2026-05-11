<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoMercado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos_mercado';

    protected $fillable = [
        'nombre',
        'unidad_empaque',
        'imagen',
        'tipo_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoProductoMercado::class, 'tipo_id');
    }

    public function getImagenUrlAttribute(): string
    {
        if ($this->imagen) {
            return asset('uploads/productos-mercado/' . $this->imagen);
        }

        return '';
    }

    public function hasImagen(): bool
    {
        return !empty($this->imagen);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
