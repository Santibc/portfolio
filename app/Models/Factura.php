<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'numero',
        'serie',
        'cliente_id',
        'obra_id',
        'fecha_emision',
        'fecha_vencimiento',
        'base_imponible',
        'iva_porcentaje',
        'iva_importe',
        'retencion_porcentaje',
        'retencion_importe',
        'total',
        'estado',
        'fecha_cobro',
        'pdf_path',
        'notas',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_cobro' => 'date',
        'base_imponible' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_importe' => 'decimal:2',
        'retencion_porcentaje' => 'decimal:2',
        'retencion_importe' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function lineas(): HasMany
    {
        return $this->hasMany(FacturaLinea::class)->orderBy('orden');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class);
    }

    // Métodos
    public function calcularTotales(): void
    {
        $baseImponible = $this->lineas->sum('importe');
        $this->base_imponible = $baseImponible;
        $this->iva_importe = $baseImponible * ($this->iva_porcentaje / 100);
        $this->retencion_importe = $baseImponible * ($this->retencion_porcentaje / 100);
        $this->total = $baseImponible + $this->iva_importe - $this->retencion_importe;
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['emitida', 'enviada']);
    }

    public function scopeCobradas($query)
    {
        return $query->where('estado', 'cobrada');
    }

    public function scopeDelAnio($query, $anio = null)
    {
        return $query->whereYear('fecha_emision', $anio ?? now()->year);
    }
}
