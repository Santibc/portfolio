<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    const UPDATED_AT = null;

    protected $fillable = ['usuario_id', 'tipo', 'titulo', 'contenido', 'url', 'leida', 'leida_en'];

    protected $casts = [
        'leida' => 'boolean',
        'leida_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }
}
