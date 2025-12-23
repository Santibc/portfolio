<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpiInventario extends Model
{
    protected $table = 'epi_inventario';

    protected $fillable = [
        'epi_catalogo_id',
        'numero_serie',
        'fecha_compra',
        'fecha_caducidad',
        'coste',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_caducidad' => 'date',
        'coste' => 'decimal:2',
    ];

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(EpiCatalogo::class, 'epi_catalogo_id');
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(EpiEntrega::class);
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(EpiRevision::class);
    }

    public function entregaActual()
    {
        return $this->entregas()->whereNull('fecha_devolucion')->first();
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }
}
