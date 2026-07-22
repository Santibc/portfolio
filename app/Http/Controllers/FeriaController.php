<?php

namespace App\Http\Controllers;

use App\Exports\FeriaInventarioExport;
use App\Models\Feria;
use App\Models\ListaPrecio;
use App\Models\PrecioProducto;
use App\Models\PrecioVariante;
use App\Models\Producto;
use App\Models\StockProducto;
use App\Models\TrasladoStock;
use App\Models\Ubicacion;
use App\Models\VarianteProducto;
use App\Services\FeriaService;
use Illuminate\Http\Request;

class FeriaController extends Controller
{
    protected FeriaService $feriaService;

    public function __construct(FeriaService $feriaService)
    {
        $this->feriaService = $feriaService;
    }

    public function index()
    {
        $ferias = Feria::with(['ubicacion', 'listaPrecio', 'creador'])
            ->orderByDesc('created_at')
            ->get();

        return view('ferias.index', compact('ferias'));
    }

    public function create()
    {
        $ubicaciones = Ubicacion::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'tipo']);
        $listas = ListaPrecio::where('activo', true)->orderBy('orden')->get(['id', 'nombre']);

        return view('ferias.form', compact('ubicaciones', 'listas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'lista_precio_base_id' => 'required|exists:listas_precios,id',
            'ubicacion_modo' => 'required|in:nueva,existente',
            'ubicacion_id' => 'required_if:ubicacion_modo,existente|nullable|exists:ubicaciones,id',
            'ubicacion_nombre' => 'required_if:ubicacion_modo,nueva|nullable|string|max:150',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'notas' => 'nullable|string|max:1000',
        ], [
            'ubicacion_id.required_if' => 'Debes seleccionar la ubicación existente.',
            'ubicacion_nombre.required_if' => 'Debes indicar el nombre de la ubicación nueva.',
            'fecha_fin.after_or_equal' => 'La fecha fin no puede ser anterior a la fecha inicio.',
        ]);

        try {
            $feria = $this->feriaService->crearFeria($request->all(), auth()->id());
        } catch (\Throwable $e) {
            \Log::error('Error al crear feria', ['mensaje' => $e->getMessage()]);
            return back()->withInput()->with('error', 'No se pudo crear la feria: ' . $e->getMessage());
        }

        return redirect()->route('ferias.show', $feria->id)
            ->with('success', 'Feria creada. Ya puedes preparar su inventario y ajustar sus precios.');
    }

    public function show($id)
    {
        $feria = Feria::with(['ubicacion', 'listaPrecio', 'listaPrecioBase', 'caja', 'creador'])
            ->findOrFail($id);

        $tieneInventario = StockProducto::where('ubicacion_id', $feria->ubicacion_id)
            ->where('cantidad_disponible', '>', 0)
            ->exists();

        return view('ferias.show', compact('feria', 'tieneInventario'));
    }

    public function activar($id)
    {
        $feria = Feria::findOrFail($id);
        if ($feria->estaCerrada()) {
            return back()->with('error', 'Una feria cerrada no se puede reactivar.');
        }

        // No se puede activar sin inventario cargado en el stand (nada para vender).
        $tieneInventario = StockProducto::where('ubicacion_id', $feria->ubicacion_id)
            ->where('cantidad_disponible', '>', 0)
            ->exists();
        if (!$tieneInventario) {
            return back()->with('error', 'No puedes activar la feria sin inventario. Carga al menos un producto al stand (desde el CEDI) antes de activarla.');
        }

        $feria->update(['estado' => Feria::ESTADO_ACTIVA]);

        return back()->with('success', 'Feria activada. Ya se puede vender con sus precios en la caja de la feria.');
    }

    public function cerrar($id)
    {
        $feria = Feria::findOrFail($id);
        $feria->update(['estado' => Feria::ESTADO_CERRADA]);

        return back()->with('success', 'Feria cerrada.');
    }

    /**
     * Buscar productos/variantes con su precio ACTUAL en la lista de la feria (ajuste ágil).
     */
    public function buscarProductos(Request $request, $id)
    {
        $feria = Feria::findOrFail($id);
        $listaId = $feria->lista_precio_id;
        $termino = trim($request->get('q', ''));

        if (mb_strlen($termino) < 2) {
            return response()->json(['data' => []]);
        }

        $productos = Producto::activos()
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('referencia', 'like', "%{$termino}%")
                    ->orWhere('codigo_barras', 'like', "%{$termino}%");
            })
            ->with('variantes')
            ->limit(40)
            ->get();

        // Filas de "producto" (todos los tonos / producto simple) primero, luego cada tono.
        $filasProducto = [];
        $filasVariante = [];
        foreach ($productos as $producto) {
            if ($producto->tiene_variantes && $producto->variantes->isNotEmpty()) {
                // Ajuste rápido de TODA la línea (todos los tonos a la vez).
                $filasProducto[] = [
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null,
                    'todas_variantes' => true,
                    'referencia' => $producto->referencia,
                    'nombre' => $producto->nombre . '  ·  TODOS los tonos',
                    'precio' => null,
                ];
                foreach ($producto->variantes as $variante) {
                    $filasVariante[] = [
                        'producto_id' => $producto->id,
                        'variante_producto_id' => $variante->id,
                        'todas_variantes' => false,
                        'referencia' => $variante->referencia_variante ?: $producto->referencia,
                        'nombre' => $producto->nombre . ($variante->nombre_variante ? ' — ' . $variante->nombre_variante : ''),
                        'precio' => $variante->getPrecioFinal($listaId),
                    ];
                }
            } else {
                $filasProducto[] = [
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null,
                    'todas_variantes' => false,
                    'referencia' => $producto->referencia,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->getPrecioPorLista($listaId),
                ];
            }
        }

        $filas = array_merge($filasProducto, $filasVariante);

        return response()->json(['data' => array_slice($filas, 0, 40)]);
    }

    /**
     * Ajuste ágil: fija el precio ABSOLUTO de un producto/variante en la lista de la feria.
     */
    public function actualizarPrecio(Request $request, $id)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'variante_producto_id' => 'nullable|integer|exists:variantes_productos,id',
            'precio' => 'required|numeric|min:0',
            'aplicar_todas_variantes' => 'nullable|boolean',
        ]);

        $feria = Feria::findOrFail($id);
        $precio = round((float) $request->precio, 2);
        $productoId = (int) $request->producto_id;

        // "Todos los tonos": aplica el mismo precio a todas las variantes del producto.
        if ($request->boolean('aplicar_todas_variantes')) {
            $producto = Producto::with('variantes')->findOrFail($productoId);
            if ($producto->variantes->isNotEmpty()) {
                foreach ($producto->variantes as $v) {
                    $this->feriaService->fijarPrecioFeria($feria, $productoId, $v->id, $precio);
                }
                return response()->json([
                    'success' => true,
                    'mensaje' => 'Precio aplicado a los ' . $producto->variantes->count() . ' tonos.',
                    'precio' => $precio,
                ]);
            }
        }

        $varianteId = $request->variante_producto_id ? (int) $request->variante_producto_id : null;
        $this->feriaService->fijarPrecioFeria($feria, $productoId, $varianteId, $precio);

        return response()->json(['success' => true, 'mensaje' => 'Precio actualizado en la feria.', 'precio' => $precio]);
    }

    /**
     * F2 — Precios masivos / promociones sobre una selección (precio fijo, descuento % o aumento %).
     */
    public function preciosMasivos(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required|in:fijo,descuento_pct,aumento_pct',
            'valor' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer|exists:productos,id',
            'items.*.variante_producto_id' => 'nullable|integer|exists:variantes_productos,id',
            'items.*.todas_variantes' => 'nullable|boolean',
        ], [
            'items.required' => 'Debes seleccionar al menos un producto.',
        ]);

        if ($request->tipo === 'descuento_pct' && $request->valor > 100) {
            return response()->json(['error' => 'El descuento no puede ser mayor a 100%.'], 422);
        }

        $feria = Feria::findOrFail($id);

        try {
            $aplicados = $this->feriaService->aplicarPreciosMasivos(
                $feria,
                $request->input('items'),
                $request->tipo,
                (float) $request->valor
            );
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Promoción aplicada a ' . count($aplicados) . ' producto(s) de la feria.',
            'aplicados' => $aplicados,
        ]);
    }

    /**
     * F3 — Inventario actual en el stand de la feria.
     */
    public function inventario($id)
    {
        $feria = Feria::findOrFail($id);

        $rows = StockProducto::with(['producto', 'variante'])
            ->where('ubicacion_id', $feria->ubicacion_id)
            ->where(function ($q) {
                $q->where('cantidad_disponible', '>', 0)->orWhere('cantidad_reservada', '>', 0);
            })
            ->get()
            ->map(function ($s) {
                $nombre = $s->producto?->nombre ?? 'N/A';
                if ($s->variante) {
                    $nombre .= ' — ' . ($s->variante->nombre_variante ?? ($s->variante->referencia_variante ?? ''));
                }
                return [
                    'producto_id' => $s->producto_id,
                    'variante_producto_id' => $s->variante_producto_id,
                    'nombre' => $nombre,
                    'disponible' => (int) $s->cantidad_disponible,
                    'reservado' => (int) $s->cantidad_reservada,
                ];
            })
            ->values();

        return response()->json(['data' => $rows]);
    }

    /**
     * F3 — Buscar productos con stock disponible en la Bodega Principal (para cargar al stand).
     */
    public function buscarProductosBodega(Request $request, $id)
    {
        Feria::findOrFail($id);
        $termino = trim($request->get('q', ''));
        if (mb_strlen($termino) < 2) {
            return response()->json(['data' => []]);
        }

        $bodega = Ubicacion::where('tipo', Ubicacion::TIPO_BODEGA)->where('es_principal', true)->firstOrFail();

        $productos = Producto::activos()
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('referencia', 'like', "%{$termino}%")
                    ->orWhere('codigo_barras', 'like', "%{$termino}%");
            })
            ->with('variantes')
            ->limit(40)
            ->get();

        $disponibleBodega = function ($productoId, $varianteId) use ($bodega) {
            $s = StockProducto::where('producto_id', $productoId)
                ->where('ubicacion_id', $bodega->id)
                ->when($varianteId, fn($x) => $x->where('variante_producto_id', $varianteId), fn($x) => $x->whereNull('variante_producto_id'))
                ->first();
            return $s ? max(0, $s->cantidad_disponible - $s->cantidad_reservada) : 0;
        };

        $filas = [];
        foreach ($productos as $producto) {
            if ($producto->tiene_variantes && $producto->variantes->isNotEmpty()) {
                foreach ($producto->variantes as $variante) {
                    $filas[] = [
                        'producto_id' => $producto->id,
                        'variante_producto_id' => $variante->id,
                        'referencia' => $variante->referencia_variante ?: $producto->referencia,
                        'nombre' => $producto->nombre . ($variante->nombre_variante ? ' — ' . $variante->nombre_variante : ''),
                        'disponible_bodega' => $disponibleBodega($producto->id, $variante->id),
                    ];
                }
            } else {
                $filas[] = [
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null,
                    'referencia' => $producto->referencia,
                    'nombre' => $producto->nombre,
                    'disponible_bodega' => $disponibleBodega($producto->id, null),
                ];
            }
        }

        return response()->json(['data' => array_slice($filas, 0, 30)]);
    }

    /**
     * F3 — Excel del inventario de la feria (cargado / vendido / devuelto / actual),
     * para el cuadre del antes y después de la devolución.
     */
    public function exportarInventarioExcel($id)
    {
        $feria = Feria::findOrFail($id);
        $nombre = 'inventario-feria-' . \Illuminate\Support\Str::slug($feria->nombre) . '-' . now()->format('Ymd-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new FeriaInventarioExport($feria), $nombre);
    }

    /**
     * F3 — Traslados EN TRÁNSITO hacia esta feria, pendientes de recibir.
     */
    public function trasladosPendientes($id)
    {
        $feria = Feria::findOrFail($id);

        $traslados = TrasladoStock::with(['items.producto', 'items.varianteProducto', 'ubicacionOrigen'])
            ->where('ubicacion_destino_id', $feria->ubicacion_id)
            ->where('estado', TrasladoStock::ESTADO_EN_TRANSITO)
            ->orderByDesc('id')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'numero' => $t->numero_traslado,
                    'origen' => $t->ubicacionOrigen?->nombre ?? '—',
                    'enviado_en' => $t->enviado_en?->format('d/m/Y H:i'),
                    'items' => $t->items->map(function ($i) {
                        $nombre = $i->producto?->nombre ?? 'N/A';
                        if ($i->varianteProducto && $i->varianteProducto->nombre_variante) {
                            $nombre .= ' — ' . $i->varianteProducto->nombre_variante;
                        }
                        return ['nombre' => $nombre, 'cantidad' => (int) $i->cantidad];
                    }),
                ];
            });

        return response()->json(['data' => $traslados]);
    }

    /**
     * F3 — Recibir en la feria un traslado en tránsito (suma el stock al stand).
     */
    public function recibirTraslado($id, $trasladoId)
    {
        $feria = Feria::findOrFail($id);
        $traslado = TrasladoStock::with('items')->findOrFail($trasladoId);

        if ((int) $traslado->ubicacion_destino_id !== (int) $feria->ubicacion_id) {
            return response()->json(['error' => 'Ese traslado no es hacia esta feria.'], 422);
        }

        try {
            $this->feriaService->recibirTraslado($traslado, auth()->id());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'mensaje' => 'Traslado recibido. El stock ya está en el stand.']);
    }

    /**
     * F3 — Cargar inventario al stand (un solo paso desde la Bodega Principal).
     */
    public function prepararInventario(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer|exists:productos,id',
            'items.*.variante_producto_id' => 'nullable|integer|exists:variantes_productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        $feria = Feria::findOrFail($id);

        try {
            $traslado = $this->feriaService->prepararInventario($feria, $request->input('items'), auth()->id());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Inventario cargado al stand (traslado ' . $traslado->numero_traslado . ').',
        ]);
    }

    /**
     * F3 — Devolver TODO el inventario disponible del stand a la Bodega Principal (cierre de feria).
     */
    public function devolverTodo($id)
    {
        $feria = Feria::findOrFail($id);

        try {
            $traslado = $this->feriaService->devolverTodoInventario($feria, auth()->id());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'mensaje' => 'Todo el inventario del stand fue devuelto a la Bodega Principal (traslado ' . $traslado->numero_traslado . ').',
        ]);
    }

    /**
     * F3 — Devolver una línea del stand a la Bodega Principal.
     */
    public function devolverInventario(Request $request, $id)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'variante_producto_id' => 'nullable|integer|exists:variantes_productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $feria = Feria::findOrFail($id);

        try {
            $this->feriaService->devolverInventario($feria, [
                'producto_id' => (int) $request->producto_id,
                'variante_producto_id' => $request->variante_producto_id ? (int) $request->variante_producto_id : null,
                'cantidad' => (int) $request->cantidad,
            ], auth()->id());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'mensaje' => 'Inventario devuelto a la Bodega Principal.']);
    }
}
