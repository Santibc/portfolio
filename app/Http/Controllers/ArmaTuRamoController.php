<?php

namespace App\Http\Controllers;

use App\Models\FlorDisponible;
use App\Models\TamanoRamo;
use App\Models\ProductoAdicional;
use App\Models\RamoPersonalizado;
use App\Models\Carrito;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ArmaTuRamoController extends Controller
{
    protected function getEmpresa()
    {
        return Empresa::first();
    }

    public function index()
    {
        $empresa = $this->getEmpresa();
        $carrito = Carrito::obtenerOCrear(Session::getId(), $empresa->id);
        $flores = FlorDisponible::disponibles()->ordenado()->get();
        $tamanos = TamanoRamo::activos()->ordenado()->get();
        $adicionales = ProductoAdicional::disponibles()->ordenado()->get()
            ->groupBy('categoria');

        // Obtener o crear ramo en progreso
        $ramoEnProgreso = RamoPersonalizado::obtenerOCrear(Session::getId());

        return view('tienda.arma-tu-ramo', compact('empresa', 'carrito', 'flores', 'tamanos', 'adicionales', 'ramoEnProgreso'));
    }

    public function seleccionarTamano(Request $request)
    {
        $request->validate([
            'tamano_id' => 'required|exists:tamanos_ramo,id',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $tamano = TamanoRamo::findOrFail($request->tamano_id);

        $ramo->update([
            'tamano_ramo_id' => $tamano->id,
            'precio_base' => $tamano->precio_base,
        ]);

        $ramo->calcularTotales()->save();

        return response()->json([
            'success' => true,
            'tamano' => $tamano,
            'ramo' => $ramo,
            'total' => $ramo->total,
        ]);
    }

    public function agregarFlor(Request $request)
    {
        $request->validate([
            'flor_id' => 'required|exists:flores_disponibles,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $flor = FlorDisponible::findOrFail($request->flor_id);

        // Verificar stock
        if ($flor->stock < $request->cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficiente stock de ' . $flor->nombre,
            ], 422);
        }

        // Verificar límites del tamaño
        $tamano = $ramo->tamano;
        if ($tamano) {
            $totalActual = $ramo->total_flores;
            if (($totalActual + $request->cantidad) > $tamano->cantidad_flores_max) {
                return response()->json([
                    'success' => false,
                    'message' => "El tamaño {$tamano->nombre} permite máximo {$tamano->cantidad_flores_max} flores",
                ], 422);
            }
        }

        // Agregar o actualizar flor
        $flores = $ramo->flores_seleccionadas ?? [];
        $encontrada = false;

        foreach ($flores as &$f) {
            if ($f['flor_id'] == $flor->id) {
                $f['cantidad'] += $request->cantidad;
                $encontrada = true;
                break;
            }
        }

        if (!$encontrada) {
            $flores[] = [
                'flor_id' => $flor->id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $flor->precio_unidad,
            ];
        }

        $ramo->flores_seleccionadas = $flores;
        $ramo->calcularTotales()->save();

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'flores' => $ramo->resumen_flores,
            'total_flores' => $ramo->total_flores,
            'total' => $ramo->total,
        ]);
    }

    public function actualizarFlor(Request $request)
    {
        $request->validate([
            'flor_id' => 'required|exists:flores_disponibles,id',
            'cantidad' => 'required|integer|min:0',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $flor = FlorDisponible::findOrFail($request->flor_id);

        $flores = $ramo->flores_seleccionadas ?? [];

        if ($request->cantidad == 0) {
            // Eliminar flor
            $flores = array_filter($flores, fn($f) => $f['flor_id'] != $flor->id);
        } else {
            // Verificar stock
            if ($flor->stock < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente',
                ], 422);
            }

            // Actualizar cantidad
            foreach ($flores as &$f) {
                if ($f['flor_id'] == $flor->id) {
                    $f['cantidad'] = $request->cantidad;
                    break;
                }
            }
        }

        $ramo->flores_seleccionadas = array_values($flores);
        $ramo->calcularTotales()->save();

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'flores' => $ramo->resumen_flores,
            'total_flores' => $ramo->total_flores,
            'total' => $ramo->total,
        ]);
    }

    public function quitarFlor(Request $request)
    {
        $request->validate([
            'flor_id' => 'required|exists:flores_disponibles,id',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        $flores = array_filter(
            $ramo->flores_seleccionadas ?? [],
            fn($f) => $f['flor_id'] != $request->flor_id
        );

        $ramo->flores_seleccionadas = array_values($flores);
        $ramo->calcularTotales()->save();

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'flores' => $ramo->resumen_flores,
            'total_flores' => $ramo->total_flores,
            'total' => $ramo->total,
        ]);
    }

    public function toggleAdicional(Request $request)
    {
        $request->validate([
            'adicional_id' => 'required|exists:productos_adicionales,id',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $adicional = ProductoAdicional::findOrFail($request->adicional_id);

        $adicionales = $ramo->adicionales ?? [];
        $encontrado = false;

        foreach ($adicionales as $key => $a) {
            if ($a['adicional_id'] == $adicional->id) {
                // Quitar si ya existe
                unset($adicionales[$key]);
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            // Agregar
            $adicionales[] = [
                'adicional_id' => $adicional->id,
                'cantidad' => 1,
                'precio' => $adicional->precio,
                'nombre' => $adicional->nombre,
            ];
        }

        $ramo->adicionales = array_values($adicionales);
        $ramo->calcularTotales()->save();

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'adicionales' => $ramo->adicionales,
            'subtotal_adicionales' => $ramo->subtotal_adicionales,
            'total' => $ramo->total,
            'agregado' => !$encontrado,
        ]);
    }

    public function guardarMensaje(Request $request)
    {
        $request->validate([
            'mensaje' => 'nullable|string|max:250',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $ramo->update(['mensaje_tarjeta' => $request->mensaje]);

        return response()->json([
            'success' => true,
            'mensaje' => $ramo->mensaje_tarjeta,
            'caracteres' => strlen($ramo->mensaje_tarjeta ?? ''),
        ]);
    }

    public function agregarAlCarrito(Request $request)
    {
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        // Validar que tiene flores
        if (empty($ramo->flores_seleccionadas) || $ramo->total_flores == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar al menos una flor',
            ], 422);
        }

        // Validar mínimo del tamaño
        $tamano = $ramo->tamano;
        if ($tamano && $ramo->total_flores < $tamano->cantidad_flores_min) {
            return response()->json([
                'success' => false,
                'message' => "El tamaño {$tamano->nombre} requiere mínimo {$tamano->cantidad_flores_min} flores",
            ], 422);
        }

        $empresa = $this->getEmpresa();
        $carrito = Carrito::obtenerOCrear(Session::getId(), $empresa->id);

        // Crear nombre descriptivo del ramo
        $nombreRamo = "Ramo Personalizado";
        if ($tamano) {
            $nombreRamo .= " - " . $tamano->nombre;
        }
        $nombreRamo .= " ({$ramo->total_flores} flores)";

        // Agregar al carrito como item especial
        $items = $carrito->items ?? [];

        $key = 'ramo-personalizado-' . $ramo->id;

        $items[$key] = [
            'key' => $key,
            'producto_id' => null,
            'variante_id' => null,
            'es_ramo_personalizado' => true,
            'ramo_id' => $ramo->id,
            'cantidad' => 1,
            'precio' => $ramo->total,
            'precio_total' => $ramo->total,
            'nombre' => $nombreRamo,
            'referencia' => 'RAMO-' . str_pad($ramo->id, 6, '0', STR_PAD_LEFT),
            'info_variante' => null,
            'detalle_ramo' => [
                'tamano' => $tamano ? $tamano->nombre : null,
                'flores' => $ramo->resumen_flores,
                'adicionales' => $ramo->adicionales,
                'mensaje_tarjeta' => $ramo->mensaje_tarjeta,
                'subtotal_flores' => $ramo->subtotal_flores,
                'subtotal_adicionales' => $ramo->subtotal_adicionales,
                'precio_base' => $ramo->precio_base,
            ],
        ];

        $carrito->items = $items;
        $carrito->calcularSubtotal();
        $carrito->save();

        // Limpiar ramo personalizado para poder crear otro
        $ramo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ramo agregado al carrito',
            'carrito_count' => count($carrito->items),
            'carrito_total' => $carrito->subtotal,
        ]);
    }

    public function reiniciar()
    {
        $ramo = RamoPersonalizado::where('session_id', Session::getId())->first();

        if ($ramo) {
            $ramo->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Configurador reiniciado',
        ]);
    }

    public function getEstado()
    {
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'tamano' => $ramo->tamano,
            'flores' => $ramo->resumen_flores,
            'adicionales' => $ramo->adicionales,
            'total_flores' => $ramo->total_flores,
            'subtotal_flores' => $ramo->subtotal_flores,
            'subtotal_adicionales' => $ramo->subtotal_adicionales,
            'precio_base' => $ramo->precio_base,
            'total' => $ramo->total,
            'mensaje_tarjeta' => $ramo->mensaje_tarjeta,
        ]);
    }
}
