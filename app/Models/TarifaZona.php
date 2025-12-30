<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaZona extends Model
{
    use HasFactory;

    protected $table = 'tarifas_zona';

    protected $fillable = [
        'zona_cobertura_id',
        'nombre',
        'costo_base',
        'costo_por_km',
        'minimo_compra',
        'costo_si_no_alcanza_minimo',
        'envio_gratis_desde',
        'monto_envio_gratis',
        'tiempo_entrega_horas',
        'activo',
    ];

    protected $casts = [
        'costo_base' => 'decimal:2',
        'costo_por_km' => 'decimal:2',
        'minimo_compra' => 'decimal:2',
        'costo_si_no_alcanza_minimo' => 'decimal:2',
        'monto_envio_gratis' => 'decimal:2',
        'envio_gratis_desde' => 'boolean',
        'tiempo_entrega_horas' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function zona()
    {
        return $this->belongsTo(ZonaCobertura::class, 'zona_cobertura_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // Métodos de cálculo
    public function calcularCosto($subtotal, $distancia = null)
    {
        // Si el subtotal supera el monto para envío gratis
        if ($this->envio_gratis_desde && $subtotal >= $this->monto_envio_gratis) {
            return 0;
        }

        $costo = $this->costo_base;

        // Agregar costo por distancia si aplica
        if ($distancia && $this->costo_por_km) {
            $costo += ($distancia * $this->costo_por_km);
        }

        // Verificar si alcanza el mínimo de compra
        if ($subtotal < $this->minimo_compra && $this->costo_si_no_alcanza_minimo) {
            $costo += $this->costo_si_no_alcanza_minimo;
        }

        return round($costo, 2);
    }

    public function cumpleMinimo($subtotal)
    {
        return $subtotal >= $this->minimo_compra;
    }
}
