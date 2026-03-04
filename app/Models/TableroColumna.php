<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableroColumna extends Model
{
    protected $table = 'tablero_columnas';

    protected $fillable = ['tablero_id', 'nombre', 'posicion', 'archivada'];

    protected $casts = ['archivada' => 'boolean'];

    public function tablero(): BelongsTo
    {
        return $this->belongsTo(Tablero::class);
    }

    public function tarjetas(): HasMany
    {
        return $this->hasMany(Tarjeta::class, 'columna_id')
            ->where('archivada', false)
            ->orderBy('posicion');
    }

    public function todasLasTarjetas(): HasMany
    {
        return $this->hasMany(Tarjeta::class, 'columna_id')->orderBy('posicion');
    }
}
