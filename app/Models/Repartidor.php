<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repartidor extends Model
{
    use HasFactory;

    protected $table = 'repartidores';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'telefono',
        'documento',
        'vehiculo',
        'placa',
        'activo',
        'notas'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function comprasHoy()
    {
        return $this->hasMany(Compra::class)->whereDate('asignado_repartidor_at', today());
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Obtener el conteo de entregas pendientes
     */
    public function entregasPendientes()
    {
        return $this->compras()
            ->whereIn('estado', ['pagada', 'enviada'])
            ->count();
    }

    /**
     * Obtener el conteo de entregas del dia
     */
    public function entregasDelDia()
    {
        return $this->compras()
            ->whereDate('fecha_entrega_deseada', today())
            ->count();
    }
}
