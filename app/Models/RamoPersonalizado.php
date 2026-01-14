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
        'estilo_ramo_id',
        'envoltura_ramo_id',
        'paso_actual',
        'tamano_ramo_id',
        'flores_seleccionadas',
        'adicionales',
        'subtotal_flores',
        'subtotal_adicionales',
        'precio_base',
        'precio_envoltura',
        'total',
        'mensaje_tarjeta',
    ];

    protected $casts = [
        'flores_seleccionadas' => 'array',
        'adicionales' => 'array',
        'subtotal_flores' => 'decimal:2',
        'subtotal_adicionales' => 'decimal:2',
        'precio_base' => 'decimal:2',
        'precio_envoltura' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function estilo()
    {
        return $this->belongsTo(EstiloRamo::class, 'estilo_ramo_id');
    }

    public function envoltura()
    {
        return $this->belongsTo(EnvolturaRamo::class, 'envoltura_ramo_id');
    }

    // Note: tamano() relationship removed - using estilo() instead for new wizard flow

    /**
     * Calcular totales del ramo
     * Incluye: precio base del estilo + subtotal flores + precio envoltura + adicionales
     */
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

        // Precio base del estilo (nuevo sistema)
        $precioBase = $this->estilo ? $this->estilo->precio_base : 0;

        // Precio de la envoltura
        $precioEnvoltura = $this->envoltura ? $this->envoltura->precio_adicional : 0;

        $this->subtotal_flores = $subtotalFlores;
        $this->subtotal_adicionales = $subtotalAdicionales;
        $this->precio_base = $precioBase;
        $this->precio_envoltura = $precioEnvoltura;
        $this->total = $subtotalFlores + $subtotalAdicionales + $precioBase + $precioEnvoltura;

        return $this;
    }

    /**
     * Obtener total de flores seleccionadas
     */
    public function getTotalFloresAttribute()
    {
        if (!$this->flores_seleccionadas) {
            return 0;
        }

        return collect($this->flores_seleccionadas)->sum('cantidad');
    }

    /**
     * Obtener resumen detallado de flores
     */
    public function getResumenFloresAttribute()
    {
        if (!$this->flores_seleccionadas) {
            return [];
        }

        return collect($this->flores_seleccionadas)->map(function ($flor) {
            $florModel = FlorDisponible::find($flor['flor_id']);
            return [
                'flor_id' => $flor['flor_id'],
                'nombre' => $florModel ? $florModel->nombre : 'Flor',
                'color' => $florModel ? $florModel->color : '',
                'imagen' => $florModel ? $florModel->imagen_url : '',
                'cantidad' => $flor['cantidad'],
                'precio_unitario' => $flor['precio_unitario'],
                'subtotal' => $flor['cantidad'] * $flor['precio_unitario'],
            ];
        })->toArray();
    }

    /**
     * Verificar si el ramo cumple con los requisitos de flores del estilo
     */
    public function cumpleRequisitoFlores()
    {
        if (!$this->estilo) {
            return false;
        }

        $totalFlores = $this->total_flores;
        return $totalFlores >= $this->estilo->flores_minimo &&
               $totalFlores <= $this->estilo->flores_maximo;
    }

    /**
     * Verificar si puede avanzar al siguiente paso
     */
    public function puedeAvanzar($pasoActual)
    {
        switch ($pasoActual) {
            case 1: // Paso 1: Estilo seleccionado
                return $this->estilo_ramo_id !== null;
            case 2: // Paso 2: Flores seleccionadas (mínimo requerido)
                return $this->cumpleRequisitoFlores();
            case 3: // Paso 3: Envoltura seleccionada
                return $this->envoltura_ramo_id !== null;
            case 4: // Paso 4: Resumen (siempre puede finalizar si llegó aquí)
                return true;
            default:
                return false;
        }
    }

    /**
     * Obtener o crear ramo para la sesión actual
     */
    public static function obtenerOCrear($sessionId)
    {
        return self::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'estilo_ramo_id' => null,
                'envoltura_ramo_id' => null,
                'paso_actual' => 1,
                'tamano_ramo_id' => null,
                'flores_seleccionadas' => [],
                'adicionales' => [],
            ]
        );
    }

    /**
     * Reiniciar el ramo a su estado inicial
     */
    public function reiniciar()
    {
        $this->update([
            'estilo_ramo_id' => null,
            'envoltura_ramo_id' => null,
            'paso_actual' => 1,
            'tamano_ramo_id' => null,
            'flores_seleccionadas' => [],
            'adicionales' => [],
            'subtotal_flores' => 0,
            'subtotal_adicionales' => 0,
            'precio_base' => 0,
            'precio_envoltura' => 0,
            'total' => 0,
            'mensaje_tarjeta' => null,
        ]);

        return $this;
    }

    /**
     * Obtener resumen completo para el carrito
     */
    public function getResumenCompletoAttribute()
    {
        return [
            'estilo' => $this->estilo ? [
                'nombre' => $this->estilo->nombre,
                'precio_base' => $this->estilo->precio_base,
            ] : null,
            'envoltura' => $this->envoltura ? [
                'nombre' => $this->envoltura->nombre,
                'precio' => $this->envoltura->precio_adicional,
            ] : null,
            'flores' => $this->resumen_flores,
            'total_flores' => $this->total_flores,
            'adicionales' => $this->adicionales,
            'mensaje_tarjeta' => $this->mensaje_tarjeta,
            'desglose' => [
                'precio_base' => $this->precio_base,
                'subtotal_flores' => $this->subtotal_flores,
                'precio_envoltura' => $this->precio_envoltura,
                'subtotal_adicionales' => $this->subtotal_adicionales,
                'total' => $this->total,
            ],
        ];
    }
}
