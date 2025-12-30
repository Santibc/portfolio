<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RamoPersonalizado extends Model
{
    use HasFactory;

    protected $table = 'ramos_personalizados';

    protected $fillable = [
        'session_id',
        'tamano_ramo_id',
        'flores_seleccionadas',
        'adicionales',
        'subtotal_flores',
        'subtotal_adicionales',
        'precio_base',
        'total',
        'mensaje_tarjeta',
    ];

    protected $casts = [
        'flores_seleccionadas' => 'array',
        'adicionales' => 'array',
        'subtotal_flores' => 'decimal:2',
        'subtotal_adicionales' => 'decimal:2',
        'precio_base' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function tamano()
    {
        return $this->belongsTo(TamanoRamo::class, 'tamano_ramo_id');
    }

    public function calcularTotales()
    {
        $subtotalFlores = 0;
        $subtotalAdicionales = 0;

        if ($this->flores_seleccionadas) {
            foreach ($this->flores_seleccionadas as $flor) {
                $subtotalFlores += ($flor['cantidad'] ?? 0) * ($flor['precio_unitario'] ?? 0);
            }
        }

        if ($this->adicionales) {
            foreach ($this->adicionales as $adicional) {
                $subtotalAdicionales += ($adicional['cantidad'] ?? 0) * ($adicional['precio'] ?? 0);
            }
        }

        $precioBase = $this->tamano ? $this->tamano->precio_base : 0;

        $this->subtotal_flores = $subtotalFlores;
        $this->subtotal_adicionales = $subtotalAdicionales;
        $this->precio_base = $precioBase;
        $this->total = $subtotalFlores + $subtotalAdicionales + $precioBase;

        return $this;
    }

    public function getTotalFloresAttribute()
    {
        if (!$this->flores_seleccionadas) return 0;

        return collect($this->flores_seleccionadas)->sum('cantidad');
    }

    public function getResumenFloresAttribute()
    {
        if (!$this->flores_seleccionadas) return [];

        return collect($this->flores_seleccionadas)->map(function ($flor) {
            $florModel = FlorDisponible::find($flor['flor_id']);
            return [
                'nombre' => $florModel ? $florModel->nombre : 'Flor',
                'color' => $florModel ? $florModel->color : '',
                'cantidad' => $flor['cantidad'],
                'precio_unitario' => $flor['precio_unitario'],
                'subtotal' => $flor['cantidad'] * $flor['precio_unitario'],
            ];
        })->toArray();
    }

    public static function obtenerOCrear($sessionId)
    {
        return self::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'tamano_ramo_id' => TamanoRamo::activos()->ordenado()->first()?->id,
                'flores_seleccionadas' => [],
                'adicionales' => [],
            ]
        );
    }
}
