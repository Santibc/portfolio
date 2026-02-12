<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaLinea extends Model
{
    protected $table = 'factura_lineas';

    public $timestamps = false;

    protected $fillable = [
        'factura_id',
        'concepto',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento_porcentaje',
        'importe',
        'orden',
        'grupo',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'importe' => 'decimal:2',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    // Calcular importe antes de guardar
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($linea) {
            $subtotal = $linea->cantidad * $linea->precio_unitario;
            $descuento = $subtotal * ($linea->descuento_porcentaje / 100);
            $linea->importe = $subtotal - $descuento;
        });
    }
}
