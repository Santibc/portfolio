<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre', 'cedula', 'direccion', 'correo', 'celular_1', 'celular_2', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'cliente_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
