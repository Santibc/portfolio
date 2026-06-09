<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoHora extends Model
{
    protected $table = 'tipo_horas';

    protected $fillable = [
        'nombre',
        'precio_hora',
        'activo',
    ];

    protected $casts = [
        'precio_hora' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function bonos(): HasMany
    {
        return $this->hasMany(TrabajadorBono::class, 'tipo_hora_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
