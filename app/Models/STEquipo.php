<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STEquipo extends Model
{
    use HasFactory;

    protected $table = 'st_equipos';

    protected $fillable = [
        'cliente_id',
        'tipo_equipo',
        'marca',
        'modelo',
        'numero_serie',
        'mac_address',
        'ip_address',
        'especificaciones',
        'fecha_compra',
        'fecha_instalacion',
        'en_garantia',
        'vencimiento_garantia',
        'ubicacion_instalacion',
        'estado',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'en_garantia' => 'boolean',
        'fecha_compra' => 'date',
        'fecha_instalacion' => 'date',
        'vencimiento_garantia' => 'date'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(STOrdenServicio::class, 'st_equipo_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEnGarantia($query)
    {
        return $query->where('en_garantia', true)
            ->where('vencimiento_garantia', '>=', now());
    }

    public function scopeOperativos($query)
    {
        return $query->where('estado', 'operativo');
    }

    public function scopeEnReparacion($query)
    {
        return $query->where('estado', 'en_reparacion');
    }

    // Accessors
    public function getDescripcionCompletaAttribute()
    {
        return "{$this->marca} {$this->modelo} - S/N: {$this->numero_serie}";
    }

    public function getGarantiaVigenteAttribute()
    {
        return $this->en_garantia && $this->vencimiento_garantia && $this->vencimiento_garantia >= now();
    }
}
