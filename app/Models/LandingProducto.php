<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\SeOrdena;
use App\Models\Concerns\TieneImagenLanding;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Categoría de la sección "Productos" de la landing. */
class LandingProducto extends Model
{
    use SeOrdena;
    use TieneImagenLanding;

    protected $table = 'landing_productos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function prefijoImagen(): string
    {
        return 'producto';
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
