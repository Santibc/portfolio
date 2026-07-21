<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacenes';

    protected $fillable = [
        'codigo',
        'nombre',
        'direccion',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function vendedores()
    {
        return $this->hasMany(User::class, 'almacen_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'almacen_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
