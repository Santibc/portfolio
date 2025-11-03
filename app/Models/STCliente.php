<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STCliente extends Model
{
    use HasFactory;

    protected $table = 'st_clientes';

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre_completo',
        'razon_social',
        'email',
        'telefono',
        'celular',
        'direccion',
        'ciudad',
        'departamento',
        'tipo_cliente',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function equipos()
    {
        return $this->hasMany(STEquipo::class, 'st_cliente_id');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(STOrdenServicio::class, 'st_cliente_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEmpresas($query)
    {
        return $query->where('tipo_cliente', 'empresa');
    }

    public function scopeParticulares($query)
    {
        return $query->where('tipo_cliente', 'particular');
    }

    // Accessors
    public function getNombreCompletoFormateadoAttribute()
    {
        if ($this->tipo_cliente === 'empresa' && $this->razon_social) {
            return $this->razon_social;
        }
        return $this->nombre_completo;
    }
}
