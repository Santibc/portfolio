<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TablaPrecioServicio extends Model
{
    protected $table = 'tabla_precios_servicios';

    protected $fillable = [
        'tipo_servicio', 'etiqueta_servicio', 'clave_calibre', 'calibre_mm',
        'largo_rango_min', 'largo_rango_max', 'cantidad_rango_min', 'cantidad_rango_max',
        'precio', 'precio_minimo',
    ];

    protected $casts = [
        'calibre_mm' => 'decimal:2',
        'precio' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
    ];
}
