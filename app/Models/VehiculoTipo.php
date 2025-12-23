<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehiculoTipo extends Model
{
    protected $table = 'vehiculo_tipos';

    protected $fillable = [
        'nombre',
    ];

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }
}
