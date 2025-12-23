<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaquinariaTipo extends Model
{
    protected $table = 'maquinaria_tipos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function maquinarias(): HasMany
    {
        return $this->hasMany(Maquinaria::class);
    }

    public function checklistPlantillas(): HasMany
    {
        return $this->hasMany(MaquinariaChecklistPlantilla::class);
    }
}
