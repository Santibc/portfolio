<?php

namespace App\Http\Controllers;

use App\Models\FlorDisponible;
use App\Models\EstiloRamo;
use App\Models\EnvolturaRamo;
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

    /**
     * Página principal - redirige al paso actual del ramo en progreso
     */
    public function index()
    {
        $empresa = $this->getEmpresa();
        $carrito = Carrito::obtenerOCrear(Session::getId(), $empresa->id);
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        // Redirigir al paso actual
        return redirect()->route('arma-tu-ramo.paso', ['paso' => $ramo->paso_actual]);
    }

    /**
     * Mostrar paso específico del wizard
     */
    public function mostrarPaso($paso)
    {
        $empresa = $this->getEmpresa();
        $carrito = Carrito::obtenerOCrear(Session::getId(), $empresa->id);
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        // Validar paso (maximo 3 pasos)
        $paso = max(1, min(3, intval($paso)));

        // Datos comunes
        $data = [
            'empresa' => $empresa,
            'carrito' => $carrito,
            'ramo' => $ramo,
            'pasoActual' => $paso,
        ];

        switch ($paso) {
            case 1:
                $data['estilos'] = EstiloRamo::activos()->ordenado()->get();
                return view('tienda.arma-tu-ramo.paso1', $data);

            case 2:
                // Verificar que tenga estilo seleccionado
                if (!$ramo->estilo_ramo_id) {
                    return redirect()->route('arma-tu-ramo.paso', ['paso' => 1])
                        ->with('error', 'Primero debes seleccionar un estilo');
                }
                $data['flores'] = FlorDisponible::disponibles()->ordenado()->get();
                $data['estilo'] = $ramo->estilo;
                return view('tienda.arma-tu-ramo.paso2', $data);

            case 3:
                // Verificar que tenga flores seleccionadas
                if (!$ramo->cumpleRequisitoFlores()) {
                    return redirect()->route('arma-tu-ramo.paso', ['paso' => 2])
                        ->with('error', 'Debes seleccionar la cantidad de flores requerida');
                }
                $data['envolturas'] = EnvolturaRamo::activos()->ordenado()->get();
                return view('tienda.arma-tu-ramo.paso3', $data);

            default:
                return redirect()->route('arma-tu-ramo.paso', ['paso' => 1]);
        }
    }

    /**
     * Paso 1: Seleccionar estilo
     */
    public function seleccionarEstilo(Request $request)
    {
        $request->validate([
            'estilo_id' => 'required|exists:estilos_ramo,id',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $estilo = EstiloRamo::findOrFail($request->estilo_id);

        // Si cambia de estilo, reiniciar flores
        if ($ramo->estilo_ramo_id !== $estilo->id) {
            $ramo->flores_seleccionadas = [];
        }

        $ramo->update([
            'estilo_ramo_id' => $estilo->id,
            'paso_actual' => 2,
        ]);

        $ramo->calcularTotales()->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'estilo' => $estilo,
                'ramo' => $ramo,
                'redirect' => route('arma-tu-ramo.paso', ['paso' => 2]),
            ]);
        }

        return redirect()->route('arma-tu-ramo.paso', ['paso' => 2]);
    }

    /**
     * Paso 2: Agregar flor
     */
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

        // Verificar límites del estilo
        $estilo = $ramo->estilo;
        if ($estilo) {
            $totalActual = $ramo->total_flores;
            if (($totalActual + $request->cantidad) > $estilo->flores_maximo) {
                return response()->json([
                    'success' => false,
                    'message' => "El estilo {$estilo->nombre} permite maximo {$estilo->flores_maximo} flores",
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
            'cumple_requisito' => $ramo->cumpleRequisitoFlores(),
        ]);
    }

    /**
     * Paso 2: Actualizar cantidad de flor
     */
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

            // Verificar límite máximo
            $estilo = $ramo->estilo;
            if ($estilo) {
                $totalSinEstaFlor = collect($flores)
                    ->where('flor_id', '!=', $flor->id)
                    ->sum('cantidad');

                if (($totalSinEstaFlor + $request->cantidad) > $estilo->flores_maximo) {
                    return response()->json([
                        'success' => false,
                        'message' => "El estilo permite maximo {$estilo->flores_maximo} flores",
                    ], 422);
                }
            }

            // Actualizar o agregar
            $encontrada = false;
            foreach ($flores as &$f) {
                if ($f['flor_id'] == $flor->id) {
                    $f['cantidad'] = $request->cantidad;
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
        }

        $ramo->flores_seleccionadas = array_values($flores);
        $ramo->calcularTotales()->save();

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'flores' => $ramo->resumen_flores,
            'total_flores' => $ramo->total_flores,
            'total' => $ramo->total,
            'cumple_requisito' => $ramo->cumpleRequisitoFlores(),
        ]);
    }

    /**
     * Paso 2: Confirmar selección de flores y avanzar
     */
    public function confirmarFlores(Request $request)
    {
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        if (!$ramo->cumpleRequisitoFlores()) {
            $estilo = $ramo->estilo;
            return response()->json([
                'success' => false,
                'message' => "Debes seleccionar entre {$estilo->flores_minimo} y {$estilo->flores_maximo} flores",
            ], 422);
        }

        $ramo->update(['paso_actual' => 3]);

        return response()->json([
            'success' => true,
            'redirect' => route('arma-tu-ramo.paso', ['paso' => 3]),
        ]);
    }

    /**
     * Paso 3: Seleccionar envoltura
     */
    public function seleccionarEnvoltura(Request $request)
    {
        $request->validate([
            'envoltura_id' => 'required|exists:envolturas_ramo,id',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $envoltura = EnvolturaRamo::findOrFail($request->envoltura_id);

        $ramo->update([
            'envoltura_ramo_id' => $envoltura->id,
            'paso_actual' => 3, // Se queda en paso 3 (wizard de 3 pasos)
        ]);

        $ramo->calcularTotales()->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'envoltura' => $envoltura,
                'ramo' => $ramo,
                'total' => $ramo->total,
            ]);
        }

        return redirect()->route('arma-tu-ramo.paso', ['paso' => 3]);
    }

    /**
     * Paso 4: Toggle adicional
     */
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
                unset($adicionales[$key]);
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
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

    /**
     * Paso 4: Guardar mensaje en tarjeta
     */
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

    /**
     * Agregar ramo al carrito
     */
    public function agregarAlCarrito(Request $request)
    {
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        // Validaciones
        if (!$ramo->estilo_ramo_id) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar un estilo',
            ], 422);
        }

        if (!$ramo->cumpleRequisitoFlores()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar la cantidad de flores requerida',
            ], 422);
        }

        if (!$ramo->envoltura_ramo_id) {
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar una envoltura',
            ], 422);
        }

        $empresa = $this->getEmpresa();
        $carrito = Carrito::obtenerOCrear(Session::getId(), $empresa->id);

        // Crear nombre descriptivo
        $nombreRamo = "Ramo Personalizado - " . $ramo->estilo->nombre;
        $nombreRamo .= " ({$ramo->total_flores} flores)";

        // Agregar al carrito
        $items = $carrito->items ?? [];
        $key = 'ramo-personalizado-' . $ramo->id;

        // Obtener imagen del estilo o primera flor seleccionada
        $imagenRamo = null;
        if ($ramo->estilo && $ramo->estilo->imagen) {
            $imagenRamo = $ramo->estilo->imagen;
        } elseif (!empty($ramo->flores_seleccionadas)) {
            $primeraFlor = FlorDisponible::find($ramo->flores_seleccionadas[0]['flor_id']);
            if ($primeraFlor && $primeraFlor->imagen) {
                $imagenRamo = $primeraFlor->imagen;
            }
        }

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
            'info_variante' => $ramo->envoltura->nombre,
            'detalle_ramo' => $ramo->resumen_completo,
            'imagen' => $imagenRamo,
        ];

        $carrito->items = $items;
        $carrito->calcularSubtotal();
        $carrito->save();

        // Limpiar ramo para poder crear otro
        $ramo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ramo agregado al carrito',
            'carrito_count' => count($carrito->items),
            'carrito_total' => $carrito->subtotal,
        ]);
    }

    /**
     * Reiniciar el configurador
     */
    public function reiniciar()
    {
        $ramo = RamoPersonalizado::where('session_id', Session::getId())->first();

        if ($ramo) {
            $ramo->reiniciar();
        }

        return response()->json([
            'success' => true,
            'message' => 'Configurador reiniciado',
            'redirect' => route('arma-tu-ramo.paso', ['paso' => 1]),
        ]);
    }

    /**
     * Ir a un paso específico (navegación)
     */
    public function irAPaso(Request $request)
    {
        $request->validate([
            'paso' => 'required|integer|min:1|max:3',
        ]);

        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());
        $pasoSolicitado = $request->paso;

        // Validar si puede ir a ese paso
        if ($pasoSolicitado > 1 && !$ramo->estilo_ramo_id) {
            return response()->json([
                'success' => false,
                'message' => 'Primero selecciona un estilo',
            ], 422);
        }

        if ($pasoSolicitado > 2 && !$ramo->cumpleRequisitoFlores()) {
            return response()->json([
                'success' => false,
                'message' => 'Primero selecciona las flores',
            ], 422);
        }

        $ramo->update(['paso_actual' => $pasoSolicitado]);

        return response()->json([
            'success' => true,
            'redirect' => route('arma-tu-ramo.paso', ['paso' => $pasoSolicitado]),
        ]);
    }

    /**
     * Obtener estado actual del ramo (AJAX)
     */
    public function getEstado()
    {
        $ramo = RamoPersonalizado::obtenerOCrear(Session::getId());

        return response()->json([
            'success' => true,
            'ramo' => $ramo,
            'estilo' => $ramo->estilo,
            'envoltura' => $ramo->envoltura,
            'flores' => $ramo->resumen_flores,
            'adicionales' => $ramo->adicionales,
            'total_flores' => $ramo->total_flores,
            'subtotal_flores' => $ramo->subtotal_flores,
            'subtotal_adicionales' => $ramo->subtotal_adicionales,
            'precio_base' => $ramo->precio_base,
            'precio_envoltura' => $ramo->precio_envoltura,
            'total' => $ramo->total,
            'mensaje_tarjeta' => $ramo->mensaje_tarjeta,
            'paso_actual' => $ramo->paso_actual,
            'cumple_requisito_flores' => $ramo->cumpleRequisitoFlores(),
        ]);
    }
}
