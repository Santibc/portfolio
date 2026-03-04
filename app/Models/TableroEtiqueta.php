<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TableroEtiqueta extends Model
{
    protected $table = 'tablero_etiquetas';

    protected $fillable = ['tablero_id', 'nombre', 'color'];

    public function tablero(): BelongsTo
    {
        return $this->belongsTo(Tablero::class);
    }

    public function tarjetas(): BelongsToMany
    {
        return $this->belongsToMany(Tarjeta::class, 'tarjeta_etiquetas', 'etiqueta_id', 'tarjeta_id');
    }
}
