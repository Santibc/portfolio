<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ListaPrecio;
use App\Models\Carrito;
use App\Models\Compra;
use App\Models\ItemCompra;
use App\Models\TransaccionPago;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\ConfiguracionPasarela;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TiendaController extends Controller
{
    /**
     * Mostrar la tienda de una empresa
     */
    public function show($slug, Request $request)
    {
        $empresa = Empresa::where('slug', $slug)
            ->where('activo', true)
            ->with(['carruselImagenesActivas'])
            ->firstOrFail();

        // Obtener primera lista de precios activa
        $listaPrecio = ListaPrecio::activas()->first();
        
        if (!$listaPrecio) {
            abort(404, 'No hay listas de precios configuradas');
        }

        // Obtener categorías con productos
        $categorias = Categoria::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->whereHas('productos', function($q) {
                $q->where('activo', true);
            })
            ->withCount([
                'productos as productos_count' => function ($q) use ($empresa) {
                    $q->where('activo', true)
                    ->where('empresa_id', $empresa->id); // quítalo si Producto no tiene empresa_id
                }
            ])
            ->orderBy('orden')
            ->get();

        // Query base de productos
        $query = Producto::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->with(['imagenPrincipal', 'categoria', 'stockPrincipal']);

        // Filtros
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('buscar')) {
            $query->buscar($request->buscar);
        }

        if ($request->filled('orden')) {
            switch ($request->orden) {
                case 'precio_asc':
                    $query->select('productos.*')
                        ->leftJoin('precios_productos', function($join) use ($listaPrecio) {
                            $join->on('productos.id', '=', 'precios_productos.producto_id')
                                 ->where('precios_productos.lista_precio_id', $listaPrecio->id)
                                 ->where('precios_productos.activo', true);
                        })
                        ->orderBy('precios_productos.precio', 'asc');
                    break;
                case 'precio_desc':
                    $query->select('productos.*')
                        ->leftJoin('precios_productos', function($join) use ($listaPrecio) {
                            $join->on('productos.id', '=', 'precios_productos.producto_id')
                                 ->where('precios_productos.lista_precio_id', $listaPrecio->id)
                                 ->where('precios_productos.activo', true);
                        })
                        ->orderBy('precios_productos.precio', 'desc');
                    break;
                case 'nombre':
                    $query->orderBy('nombre');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        // Filtro de stock
        if ($request->filled('stock') && $request->stock == '1') {
            $query->conStock();
        }

        $productos = $query->paginate(12)->withQueryString();

        // Cargar precios para la lista seleccionada
        foreach ($productos as $producto) {
            $producto->precio_actual = $producto->getPrecioPorLista($listaPrecio->id);
        }

        // Obtener carrito
        $carrito = $this->obtenerCarrito($empresa->id);

        return view('tienda.index', compact(
            'empresa',
            'productos',
            'categorias',
            'listaPrecio',
            'carrito'
        ));
    }

    /**
     * Mostrar detalle de producto
     */
    public function producto($slug, $productoId)
    {
        $empresa = Empresa::where('slug', $slug)
            ->where('activo', true)
            ->firstOrFail();

        $producto = Producto::where('id', $productoId)
            ->where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->with(['imagenes', 'categoria', 'variantes' => function($q) {
                $q->where('activo', true);
            }])
            ->firstOrFail();

        // Obtener primera lista de precios
        $listaPrecio = ListaPrecio::activas()->first();
        $producto->precio_actual = $producto->getPrecioPorLista($listaPrecio->id);

        // Si tiene variantes, cargar stock de cada una
        if ($producto->tiene_variantes) {
            $producto->load(['variantes.stock']);
        } else {
            $producto->load('stockPrincipal');
        }

        // Productos relacionados
        $relacionados = Producto::where('empresa_id', $empresa->id)
            ->where('categoria_id', $producto->categoria_id)
            ->where('id', '!=', $producto->id)
            ->where('activo', true)
            ->with('imagenPrincipal')
            ->limit(4)
            ->get();

        foreach ($relacionados as $prod) {
            $prod->precio_actual = $prod->getPrecioPorLista($listaPrecio->id);
        }

        $carrito = $this->obtenerCarrito($empresa->id);

        return view('tienda.producto', compact(
            'empresa',
            'producto',
            'relacionados',
            'listaPrecio',
            'carrito'
        ));
    }

    /**
     * Ver carrito
     */
    public function verCarrito($slug)
    {
        $empresa = Empresa::where('slug', $slug)
            ->where('activo', true)
            ->firstOrFail();

        $carrito = $this->obtenerCarrito($empresa->id);
        $listaPrecio = ListaPrecio::activas()->first();

        return view('tienda.carrito', compact('empresa', 'carrito', 'listaPrecio'));
    }

    /**
     * Agregar producto al carrito
     */
    public function agregarCarrito(Request $request, $slug)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'variante_id' => 'nullable|exists:variantes_productos,id'
        ]);

        $empresa = Empresa::where('slug', $slug)->firstOrFail();
        $producto = Producto::findOrFail($request->producto_id);
        
        // Verificar que el producto pertenece a la empresa
        if ($producto->empresa_id != $empresa->id) {
            return response()->json(['error' => 'Producto no válido'], 400);
        }

        // Verificar stock
        if (!$producto->hayStock($request->cantidad, $request->variante_id)) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        // Obtener precio
        $listaPrecio = ListaPrecio::activas()->first();
        $precio = $producto->getPrecioPorLista($listaPrecio->id);

        if (!$precio) {
            return response()->json(['error' => 'Precio no configurado'], 400);
        }

        $carrito = $this->obtenerCarrito($empresa->id);
        $carrito->agregarItem(
            $request->producto_id,
            $request->cantidad,
            $request->variante_id,
            $precio
        );

        return response()->json([
            'success' => true,
            'total_items' => $carrito->total_items,
            'subtotal' => $carrito->subtotal
        ]);
    }

    /**
     * Actualizar cantidad en carrito
     */
    public function actualizarCarrito(Request $request, $slug)
    {
        $request->validate([
            'key' => 'required|string',
            'cantidad' => 'required|integer|min:0'
        ]);

        $empresa = Empresa::where('slug', $slug)->firstOrFail();
        $carrito = $this->obtenerCarrito($empresa->id);

        if ($request->cantidad == 0) {
            $carrito->quitarItem($request->key);
        } else {
            // Verificar stock antes de actualizar
            $item = $carrito->items[$request->key] ?? null;
            if ($item) {
                $producto = Producto::find($item['producto_id']);
                if (!$producto->hayStock($request->cantidad, $item['variante_id'] ?? null)) {
                    return response()->json(['error' => 'Stock insuficiente'], 400);
                }
            }
            
            $carrito->actualizarCantidad($request->key, $request->cantidad);
        }

        return response()->json([
            'success' => true,
            'total_items' => $carrito->total_items,
            'subtotal' => $carrito->subtotal
        ]);
    }

    /**
     * Quitar item del carrito
     */
    public function quitarDelCarrito(Request $request, $slug)
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        $empresa = Empresa::where('slug', $slug)->firstOrFail();
        $carrito = $this->obtenerCarrito($empresa->id);
        $carrito->quitarItem($request->key);

        return response()->json([
            'success' => true,
            'total_items' => $carrito->total_items,
            'subtotal' => $carrito->subtotal
        ]);
    }

    /**
     * Mostrar checkout
     */
    public function checkout($slug)
    {
        $empresa = Empresa::where('slug', $slug)
            ->where('activo', true)
            ->firstOrFail();

        $carrito = $this->obtenerCarrito($empresa->id);

        if (empty($carrito->items)) {
            return redirect()->route('tienda.carrito', $slug)
                ->with('error', 'El carrito está vacío');
        }

        $departamentos = Departamento::with('ciudades')->get();
        $configuracionPasarela = ConfiguracionPasarela::obtenerConfiguracionActiva();

        return view('tienda.checkout', compact(
            'empresa',
            'carrito',
            'departamentos',
            'configuracionPasarela'
        ));
    }

    /**
     * Procesar compra
     */
    public function procesarCompra(Request $request, $slug)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id',
            'notas' => 'nullable|string'
        ]);

        $empresa = Empresa::where('slug', $slug)->firstOrFail();
        $carrito = $this->obtenerCarrito($empresa->id);

        if (empty($carrito->items)) {
            return redirect()->route('tienda.carrito', $slug)
                ->with('error', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {
            // Crear compra
            $compra = Compra::create([
                'empresa_id' => $empresa->id,
                'nombre_cliente' => $request->nombre,
                'email_cliente' => $request->email,
                'telefono_cliente' => $request->telefono,
                'direccion_envio' => $request->direccion,
                'ciudad_id' => $request->ciudad_id,
                'subtotal' => $carrito->subtotal,
                'impuestos' => 0, // Calcular según configuración
                'costo_envio' => 0, // Calcular según ciudad
                'total' => $carrito->subtotal,
                'estado' => 'pendiente',
                'notas' => $request->notas
            ]);

            // Crear items de compra
            foreach ($carrito->items as $item) {
                ItemCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $item['producto_id'],
                    'variante_producto_id' => $item['variante_id'] ?? null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'descuento' => 0,
                    'precio_total' => $item['cantidad'] * $item['precio'],
                    'referencia_producto' => $item['referencia'],
                    'nombre_producto' => $item['nombre'],
                    'info_variante' => isset($item['info_variante']) ? 
                        "Talla: {$item['info_variante']['talla']}, Color: {$item['info_variante']['color']}" : null
                ]);

                // Descontar stock
                $producto = Producto::find($item['producto_id']);
                if ($producto->controlar_stock) {
                    $stock = $producto->tiene_variantes && isset($item['variante_id']) ?
                        $producto->stock()->where('variante_producto_id', $item['variante_id'])->first() :
                        $producto->stockPrincipal;
                    
                    if ($stock) {
                        $stock->salida($item['cantidad'], 'venta', $compra->numero_compra);
                    }
                }
            }

            // Crear transacción de pago
            $transaccion = TransaccionPago::create([
                'compra_id' => $compra->id,
                'pasarela' => 'wompi',
                'monto' => $compra->total,
                'moneda' => 'COP',
                'estado' => 'pendiente'
            ]);

            // Vaciar carrito
            $carrito->vaciar();

            DB::commit();

            // Redirigir a pasarela de pago
            return $this->redirigirAPasarela($compra, $transaccion);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Confirmación de pago (webhook/callback)
     */
    public function confirmarPago(Request $request, $referencia)
    {
        $transaccion = TransaccionPago::where('referencia_transaccion', $referencia)->firstOrFail();
        
        // Aquí procesarías la respuesta de Wompi
        // Este es un ejemplo simplificado
        
        $transaccion->update([
            'estado' => 'aprobada',
            'id_transaccion_pasarela' => $request->id,
            'fecha_procesamiento' => now(),
            'respuesta_pasarela' => $request->all()
        ]);

        $transaccion->compra->update(['estado' => 'pagada']);
        $transaccion->compra->generarComision();

        return view('tienda.confirmacion', [
            'compra' => $transaccion->compra,
            'transaccion' => $transaccion
        ]);
    }

    /**
     * Obtener carrito de la sesión
     */
    private function obtenerCarrito($empresaId)
    {
        $sessionId = Session::getId();
        return Carrito::obtenerOCrear($sessionId, $empresaId);
    }

    /**
     * Redirigir a pasarela de pago Wompi
     */
    private function redirigirAPasarela($compra, $transaccion)
    {
        $wompiService = new WompiService();
        $resultado = $wompiService->crearLinkPago($compra, $transaccion);
        
        if ($resultado['success'] && $resultado['payment_url']) {
            return redirect()->away($resultado['payment_url']);
        } else {
            // Si falla, mostrar página de error o volver al checkout
            return redirect()->route('tienda.checkout', $compra->empresa->slug)
                ->with('error', 'Error al procesar el pago. Por favor intente nuevamente.');
        }
    }
}