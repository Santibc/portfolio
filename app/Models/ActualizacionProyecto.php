<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActualizacionProyecto extends Model
{
    use HasFactory;

    protected $table = 'actualizaciones_proyecto';

    protected $fillable = [
        'proyecto_id',
        'autor_id',
        'titulo',
        'contenido',
        'tipo',
        'visible_inversores',
        'publicado_at'
    ];

    protected $casts = [
        'visible_inversores' => 'boolean',
        'publicado_at' => 'datetime'
    ];

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function scopeVisibles($query)
    {
        return $query->where('visible_inversores', true);
    }
}
