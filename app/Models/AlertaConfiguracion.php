<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaConfiguracion extends Model
{
    protected $table = 'alerta_configuraciones';

    protected $fillable = [
        'tipo',
        'dias_antelacion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
