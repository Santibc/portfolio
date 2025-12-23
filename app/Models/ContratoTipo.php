<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContratoTipo extends Model
{
    protected $table = 'contrato_tipos';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }
}
