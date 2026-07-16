<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'nombre',
        'codigo',
        'ubicacion_id',
        'lista_precio_id',
        'cajero_asignado_id',
        'estado',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class, 'lista_precio_id');
    }

    public function cajeroAsignado()
    {
        return $this->belongsTo(User::class, 'cajero_asignado_id');
    }

    public function sesiones()
    {
        return $this->hasMany(SesionCaja::class);
    }

    public function ventas()
    {
        return $this->hasMany(VentaPdv::class);
    }

    public function vales()
    {
        return $this->hasMany(ValeCaja::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado', 'cerrada');
    }

    // Métodos
    public function sesionActiva()
    {
        return $this->sesiones()->where('estado', 'abierta')->first();
    }

    public function estaAbierta()
    {
        return $this->estado === 'abierta';
    }

    public function estaCerrada()
    {
        return $this->estado === 'cerrada';
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'abierta' => '<span class="badge bg-success">Abierta</span>',
            'cerrada' => '<span class="badge bg-secondary">Cerrada</span>',
            'en_cierre' => '<span class="badge bg-warning text-dark">En Cierre</span>',
            default => '<span class="badge bg-light text-dark">' . $this->estado . '</span>',
        };
    }
}
