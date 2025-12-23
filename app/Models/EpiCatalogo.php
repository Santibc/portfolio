<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpiCatalogo extends Model
{
    protected $table = 'epi_catalogo';

    protected $fillable = [
        'nombre',
        'categoria',
        'tiene_caducidad',
        'requiere_revision',
        'periodicidad_revision_meses',
    ];

    protected $casts = [
        'tiene_caducidad' => 'boolean',
        'requiere_revision' => 'boolean',
    ];

    public function inventario(): HasMany
    {
        return $this->hasMany(EpiInventario::class);
    }
}
