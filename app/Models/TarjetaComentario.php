<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarjetaComentario extends Model
{
    use SoftDeletes;

    protected $table = 'tarjeta_comentarios';

    protected $fillable = ['tarjeta_id', 'user_id', 'contenido', 'tipo'];

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
