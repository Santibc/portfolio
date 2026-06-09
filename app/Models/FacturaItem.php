<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaItem extends Model
{
    use HasFactory;

    protected $table = 'factura_items';

    protected $fillable = [
        'factura_id',
        'producto_id',
        'referencia',
        'descripcion',
        'color',
        'composicion',
        'codigo_pa',
        'pais_origen',
        'cantidad',
        'precio_unitario',
        'descuento',
        'descuento_tipo',
        'impuesto_porcentaje',
        'total_linea',
        'tallas_json',
        'orden',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto_porcentaje' => 'decimal:2',
        'total_linea' => 'decimal:2',
        'tallas_json' => 'array',
        'orden' => 'int',
    ];

    /**
     * Resuelve el monto de descuento efectivo (en moneda de la factura) a partir
     * de un valor crudo y su tipo. Para 'porcentaje' aplica el % sobre la base;
     * para 'valor' usa el monto tal cual. Nunca devuelve negativo ni excede la base.
     */
    public static function calcularDescuento(float $base, string $tipo, float $valor): float
    {
        $valor = max($valor, 0.0);
        $descuento = $tipo === 'porcentaje' ? $base * $valor / 100 : $valor;

        return round(min($descuento, max($base, 0.0)), 2);
    }

    /**
     * Monto de descuento efectivo de esta línea (cantidad × precio − descuento).
     * Fuente única de verdad usada por el cálculo de totales y por el payload Siigo.
     */
    public function descuentoValor(): float
    {
        $base = (float) $this->cantidad * (float) $this->precio_unitario;

        return self::calcularDescuento($base, $this->descuento_tipo ?? 'valor', (float) $this->descuento);
    }

    /**
     * @return BelongsTo<Factura, FacturaItem>
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * @return BelongsTo<Producto, FacturaItem>
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
