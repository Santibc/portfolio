<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'titulo',
        'contenido',
        'url_accion',
        'leida',
        'leida_at',
        'prioridad',
        'referencia_id',
        'referencia_type'
    ];

    protected $casts = [
        'leida' => 'boolean',
        'leida_at' => 'datetime'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function referencia()
    {
        return $this->morphTo();
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }
}
