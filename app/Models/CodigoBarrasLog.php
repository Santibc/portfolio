<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodigoBarrasLog extends Model
{
    use HasFactory;

    protected $table = 'codigo_barras_logs';

    protected $fillable = [
        'producto_id',
        'variante_producto_id',
        'codigo_anterior',
        'codigo_nuevo',
        'usuario_id',
        'origen',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function variante()
    {
        return $this->belongsTo(VarianteProducto::class, 'variante_producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Registrar un cambio de código de barras.
     * Si $variante es null, el cambio es a nivel de producto.
     * Si $variante existe, el cambio es a nivel de variante (y producto_id es el de la variante).
     */
    public static function registrar(
        Producto $producto,
        ?string $anterior,
        ?string $nuevo,
        string $origen,
        ?VarianteProducto $variante = null
    ): void {
        if ($anterior === $nuevo) {
            return;
        }

        self::create([
            'producto_id' => $producto->id,
            'variante_producto_id' => $variante?->id,
            'codigo_anterior' => $anterior,
            'codigo_nuevo' => $nuevo,
            'usuario_id' => auth()->id(),
            'origen' => $origen,
        ]);
    }
}
