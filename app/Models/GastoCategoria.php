<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GastoCategoria extends Model
{
    protected $table = 'gasto_categorias';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
}
