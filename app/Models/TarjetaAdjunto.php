<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarjetaAdjunto extends Model
{
    protected $table = 'tarjeta_adjuntos';

    protected $fillable = ['tarjeta_id', 'user_id', 'nombre_original', 'ruta_archivo', 'mime_type', 'tamano'];

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('uploads/tableros/' . $this->ruta_archivo);
    }
}
