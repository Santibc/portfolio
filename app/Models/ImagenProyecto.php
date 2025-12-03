<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenProyecto extends Model
{
    use HasFactory;

    protected $table = 'imagenes_proyecto';

    protected $fillable = [
        'proyecto_id',
        'ruta_imagen',
        'thumbnail',
        'titulo',
        'descripcion',
        'es_principal',
        'orden'
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'orden' => 'integer'
    ];

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function scopePrincipales($query)
    {
        return $query->where('es_principal', true);
    }

    public function scopePorOrden($query)
    {
        return $query->orderBy('orden');
    }
}
