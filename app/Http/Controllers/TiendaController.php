<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\PrecioProducto;
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
use Illuminate\Support\Facades\Log;

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
        // En TiendaController@agregarCarrito
        $empresa = Empresa::where('slug', $slug)->firstOrFail();

        // Verificar límite de transacciones del mes
        $transaccionesMes = Compra::where('empresa_id', $empresa->id)
            ->where('estado', 'pagada')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($empresa->planMembresia->limite_transacciones && 
            $transaccionesMes >= $empresa->planMembresia->limite_transacciones) {
            return response()->json([
                'error' => 'La tienda ha alcanzado el límite de ventas mensuales. Por favor contacta al vendedor.'
            ], 403);
        }
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
/**
 * Procesar compra y redirigir a Wompi
 */
public function procesarCompra(Request $request, $slug)
{
    // En TiendaController@agregarCarrito
    $empresa = Empresa::where('slug', $slug)->firstOrFail();

    // Verificar límite de transacciones del mes
    $transaccionesMes = Compra::where('empresa_id', $empresa->id)
        ->where('estado', 'pagada')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    if ($empresa->planMembresia->limite_transacciones && 
        $transaccionesMes >= $empresa->planMembresia->limite_transacciones) {
        return response()->json([
            'error' => 'La tienda ha alcanzado el límite de ventas mensuales. Por favor contacta al vendedor.'
        ], 403);
    }
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

        // Generar datos para Wompi
        $wompiService = new WompiService();
        $datosCheckout = $wompiService->generarDatosCheckout($compra, $transaccion);

        DB::commit();

        // Crear formulario HTML y enviarlo automáticamente
        return view('tienda.redirect-wompi', compact('datosCheckout'));

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error procesando compra: ' . $e->getMessage());
        return back()->with('error', 'Error al procesar la compra. Por favor intente nuevamente.');
    }
}

    /**
     * Confirmación de pago (webhook/callback)
     */
/**
 * Confirmación de pago (callback de Wompi)
 */
public function confirmarPago(Request $request, $slug, $referencia)
{
    $transaccion = TransaccionPago::where('referencia_transaccion', $referencia)->firstOrFail();
    
    // Verificar si ya fue procesada
    if ($transaccion->estado !== 'pendiente') {
        if ($transaccion->estado === 'aprobada') {
            return view('tienda.confirmacion', [
                'compra' => $transaccion->compra,
                'transaccion' => $transaccion
            ]);
        } else {
            return view('tienda.pago-rechazado', [
                'compra' => $transaccion->compra,
                'transaccion' => $transaccion
            ]);
        }
    }
    
    // Obtener ID de transacción de Wompi desde query params
    $transaccionWompiId = $request->get('id');
    
    if ($transaccionWompiId) {
        // Consultar estado en Wompi
        $wompiService = new WompiService();
        $datosTransaccion = $wompiService->consultarTransaccion($transaccionWompiId);
        
        if ($datosTransaccion) {
            $estado = $datosTransaccion['status'] ?? null;
            
            switch ($estado) {
                case 'APPROVED':
                    $transaccion->update([
                        'estado' => 'aprobada',
                        'id_transaccion_pasarela' => $transaccionWompiId,
                        'metodo_pago' => $datosTransaccion['payment_method_type'] ?? null,
                        'fecha_procesamiento' => now(),
                        'respuesta_pasarela' => $datosTransaccion,
                        'codigo_autorizacion' => $datosTransaccion['authorization_code'] ?? null
                    ]);
                    
                    // Actualizar compra
                    $transaccion->compra->update(['estado' => 'pagada']);
                    
                    // Generar comisión
                    $transaccion->compra->generarComision();
                    
                    return view('tienda.confirmacion', [
                        'compra' => $transaccion->compra,
                        'transaccion' => $transaccion
                    ]);
                    
                case 'DECLINED':
                case 'VOIDED':
                    $transaccion->update([
                        'estado' => 'rechazada',
                        'id_transaccion_pasarela' => $transaccionWompiId,
                        'mensaje_error' => $datosTransaccion['status_message'] ?? 'Transacción rechazada',
                        'respuesta_pasarela' => $datosTransaccion
                    ]);
                    
                    // Liberar stock
                    $this->liberarStockCompra($transaccion->compra);
                    
                    return view('tienda.pago-rechazado', [
                        'compra' => $transaccion->compra,
                        'transaccion' => $transaccion
                    ]);
                    
                case 'PENDING':
                    // Mostrar página de pendiente
                    return view('tienda.pago-pendiente', [
                        'empresa' => $transaccion->compra->empresa,
                        'transaccion' => $transaccion
                    ]);
                    
                default:
                    $transaccion->update([
                        'estado' => 'error',
                        'mensaje_error' => 'Estado desconocido: ' . $estado,
                        'respuesta_pasarela' => $datosTransaccion
                    ]);
                    
                    return view('tienda.pago-error', [
                        'compra' => $transaccion->compra,
                        'transaccion' => $transaccion
                    ]);
            }
        }
    }
    
    // Si no hay ID o no se pudo consultar, mostrar pendiente
    return view('tienda.pago-pendiente', [
        'empresa' => $transaccion->compra->empresa,
        'transaccion' => $transaccion
    ]);
}

/**
 * Liberar stock de una compra cancelada/rechazada
 */
private function liberarStockCompra($compra)
{
    foreach ($compra->items as $item) {
        $producto = $item->producto;
        
        if ($producto && $producto->controlar_stock) {
            $stock = $item->variante_producto_id 
                ? $producto->stock()->where('variante_producto_id', $item->variante_producto_id)->first()
                : $producto->stockPrincipal;
            
            if ($stock) {
                // Devolver el stock
                $stock->entrada(
                    $item->cantidad, 
                    'devolucion', 
                    $compra->numero_compra,
                    'Pago rechazado/cancelado'
                );
            }
        }
    }
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
    /**
     * Mostrar página de categorías con filtros
     */
/**
 * Mostrar página de categorías con filtros
 */
public function categorias($slug, Request $request)
{
    $empresa = Empresa::where('slug', $slug)
        ->where('activo', true)
        ->firstOrFail();

    // Obtener primera lista de precios activa
    $listaPrecio = ListaPrecio::activas()->first();
    
    if (!$listaPrecio) {
        abort(404, 'No hay listas de precios configuradas');
    }

    // Obtener todas las categorías con conteo de productos
    $categorias = Categoria::where('empresa_id', $empresa->id)
        ->where('activo', true)
        ->whereHas('productos', function($q) {
            $q->where('activo', true);
        })
        ->withCount([
            'productos as productos_count' => function ($q) use ($empresa) {
                $q->where('activo', true)
                ->where('empresa_id', $empresa->id);
            }
        ])
        ->orderBy('orden')
        ->get();

    // Obtener categoría seleccionada si existe
    $categoriaSeleccionada = null;
    if ($request->filled('categoria')) {
        $categoriaSeleccionada = Categoria::find($request->categoria);
    }

    // Query base de productos
    $query = Producto::where('empresa_id', $empresa->id)
        ->where('productos.activo', true)
        ->with(['imagenPrincipal', 'imagenes', 'categoria', 'stockPrincipal']);

    // Filtro por categoría
    if ($request->filled('categoria')) {
        $query->where('categoria_id', $request->categoria);
    }

    // Filtro por búsqueda
    if ($request->filled('buscar')) {
        $query->buscar($request->buscar);
    }

    // Obtener rango de precios antes de aplicar filtros de precio
    // Primero obtener todos los productos de la empresa (o categoría si está filtrada)
    $productosParaRango = Producto::where('empresa_id', $empresa->id)
        ->where('activo', true);
    
    // Si hay categoría seleccionada, aplicar ese filtro
    if ($request->filled('categoria')) {
        $productosParaRango->where('categoria_id', $request->categoria);
    }
    
    // Obtener los IDs de productos
    $productosIds = $productosParaRango->pluck('id');
    
    // Ahora obtener el rango de precios de estos productos
    $rangoPreciosQuery = PrecioProducto::whereIn('producto_id', $productosIds)
        ->where('lista_precio_id', $listaPrecio->id)
        ->where('activo', true);
    
    $precioMin = floor($rangoPreciosQuery->min('precio') ?? 0);
    $precioMax = ceil($rangoPreciosQuery->max('precio') ?? 1000000);

    // Filtro por rango de precio (select)
    if ($request->filled('rango_precio')) {
        $rango = explode('-', $request->rango_precio);
        $min = $rango[0];
        $max = $rango[1] ?? null;

        $query->whereHas('precios', function($q) use ($listaPrecio, $min, $max) {
            $q->where('lista_precio_id', $listaPrecio->id)
              ->where('activo', true)
              ->where('precio', '>=', $min);
            
            if ($max && $max > 0) {
                $q->where('precio', '<=', $max);
            }
        });
    }

    // Filtro por precio mínimo y máximo (slider)
    if ($request->filled('precio_min') || $request->filled('precio_max')) {
        $minFilter = $request->precio_min ?? $precioMin;
        $maxFilter = $request->precio_max ?? $precioMax;

        $query->whereHas('precios', function($q) use ($listaPrecio, $minFilter, $maxFilter) {
            $q->where('lista_precio_id', $listaPrecio->id)
              ->where('activo', true)
              ->whereBetween('precio', [$minFilter, $maxFilter]);
        });
    }

    // Ordenamiento
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

    // Paginación
    $porPagina = $request->get('por_pagina', 12);
    $productos = $query->paginate($porPagina)->withQueryString();

    // Cargar precios para la lista seleccionada
    foreach ($productos as $producto) {
        $producto->precio_actual = $producto->getPrecioPorLista($listaPrecio->id);
    }

    // Obtener carrito
    $carrito = $this->obtenerCarrito($empresa->id);

    return view('tienda.categoria', compact(
        'empresa',
        'productos',
        'categorias',
        'categoriaSeleccionada',
        'listaPrecio',
        'carrito',
        'precioMin',
        'precioMax'
    ));
}
}