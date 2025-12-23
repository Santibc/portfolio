<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObraTipo extends Model
{
    protected $table = 'obra_tipos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class);
    }

    public function primaConfiguraciones(): HasMany
    {
        return $this->hasMany(PrimaConfiguracion::class);
    }
}
