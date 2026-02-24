<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenBosquejo extends Model
{
    protected $table = 'orden_bosquejos';

    protected $fillable = [
        'orden_id', 'plantilla_bosquejo_id', 'tipo_origen', 'nombre',
        'ruta_archivo', 'ruta_miniatura', 'orden_visual',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function plantilla()
    {
        return $this->belongsTo(PlantillaBosquejo::class, 'plantilla_bosquejo_id');
    }

    public function piezas()
    {
        return $this->hasMany(OrdenPieza::class, 'orden_bosquejo_id');
    }
}
