<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesionCaja extends Model
{
    use HasFactory;

    protected $table = 'sesiones_caja';

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'estado',
        'monto_apertura',
        'total_ventas_efectivo',
        'total_ventas_transferencia',
        'total_ventas',
        'cantidad_ventas',
        'total_vales',
        'total_anulaciones',
        'monto_esperado_efectivo',
        'monto_contado',
        'diferencia',
        'observaciones_cierre',
        'abierta_en',
        'cerrada_en',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'total_ventas_efectivo' => 'decimal:2',
        'total_ventas_transferencia' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'total_vales' => 'decimal:2',
        'total_anulaciones' => 'decimal:2',
        'monto_esperado_efectivo' => 'decimal:2',
        'monto_contado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'abierta_en' => 'datetime',
        'cerrada_en' => 'datetime',
    ];

    // Relaciones
    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
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
    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado', 'cerrada');
    }

    // Métodos
    public function estaAbierta()
    {
        return $this->estado === 'abierta';
    }

    public function calcularTotales()
    {
        $ventas = $this->ventas()->completadas()->get();

        $this->total_ventas = $ventas->sum('total');
        $this->cantidad_ventas = $ventas->count();
        $this->total_ventas_efectivo = $ventas->sum('monto_efectivo');
        $this->total_ventas_transferencia = $ventas->sum('monto_transferencia');
        $this->total_anulaciones = $this->ventas()->anuladas()->sum('total');
        $this->total_vales = $this->vales()->whereIn('estado', ['pendiente', 'redimido'])->sum('monto');

        // Expected cash = base + cash received - change given - vouchers
        $totalCambio = $ventas->sum('cambio') ?? 0;
        $this->monto_esperado_efectivo = $this->monto_apertura + $this->total_ventas_efectivo - $totalCambio - $this->total_vales;

        $this->save();

        return $this;
    }

    public function cerrar($montoContado, $observaciones = null)
    {
        $this->calcularTotales();
        $this->monto_contado = $montoContado;
        $this->diferencia = $montoContado - $this->monto_esperado_efectivo;
        $this->observaciones_cierre = $observaciones;
        $this->estado = 'cerrada';
        $this->cerrada_en = now();
        $this->save();

        // Update caja state
        $this->caja->update(['estado' => 'cerrada']);

        return $this;
    }

    public function getDuracionAttribute()
    {
        $fin = $this->cerrada_en ?? now();
        return $this->abierta_en->diffForHumans($fin, true);
    }

    public function getDiferenciaColorAttribute()
    {
        if ($this->diferencia === null) return '';
        if ($this->diferencia == 0) return 'text-success';
        if ($this->diferencia > 0) return 'text-primary';
        return 'text-danger';
    }

    public function getDiferenciaLabelAttribute()
    {
        if ($this->diferencia === null) return '';
        if ($this->diferencia == 0) return 'Cuadre exacto';
        if ($this->diferencia > 0) return 'Sobrante';
        return 'Faltante';
    }
}
