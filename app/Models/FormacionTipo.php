<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormacionTipo extends Model
{
    protected $table = 'formacion_tipos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_horas',
        'periodicidad_meses',
        'obligatoria',
    ];

    protected $casts = [
        'obligatoria' => 'boolean',
    ];

    public function formaciones(): HasMany
    {
        return $this->hasMany(TrabajadorFormacion::class);
    }
}
