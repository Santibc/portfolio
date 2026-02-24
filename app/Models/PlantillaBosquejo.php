<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaBosquejo extends Model
{
    protected $table = 'plantillas_bosquejos';

    protected $fillable = ['grupo_bosquejo_id', 'nombre', 'ruta_archivo', 'ruta_miniatura'];

    public function grupo()
    {
        return $this->belongsTo(GrupoBosquejo::class, 'grupo_bosquejo_id');
    }

    public function ordenBosquejos()
    {
        return $this->hasMany(OrdenBosquejo::class, 'plantilla_bosquejo_id');
    }
}
