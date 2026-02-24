<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoBosquejo extends Model
{
    protected $table = 'grupos_bosquejos';

    protected $fillable = ['nombre'];

    public function plantillas()
    {
        return $this->hasMany(PlantillaBosquejo::class, 'grupo_bosquejo_id');
    }
}
