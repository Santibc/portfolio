<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaducidadGeneral extends Model
{
    protected $table = 'caducidades_generales';

    protected $fillable = [
        'tipo',
        'nombre',
        'descripcion',
        'fecha_emision',
        'fecha_caducidad',
        'documento_path',
        'alerta_activa',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_caducidad' => 'date',
        'alerta_activa' => 'boolean',
    ];

    public function getProximoACaducarAttribute(): bool
    {
        return $this->fecha_caducidad->diffInDays(now()) <= 30;
    }

    public function getCaducadoAttribute(): bool
    {
        return $this->fecha_caducidad->isPast();
    }

    // Scopes
    public function scopeConAlerta($query)
    {
        return $query->where('alerta_activa', true);
    }

    public function scopeProximasACaducar($query, int $dias = 30)
    {
        return $query->whereBetween('fecha_caducidad', [now(), now()->addDays($dias)]);
    }
}
