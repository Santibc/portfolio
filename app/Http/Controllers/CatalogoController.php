<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnlaceAcceso;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\SolicitudCotizacion;
use App\Models\ItemSolicitudCotizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CatalogoController extends Controller
{
    /**
     * Flujo A: Acceso por cliente vía link/token
     */
    public function mostrarPorToken($token)
    {
        $enlace = EnlaceAcceso::where('token', $token)->first();
        
        if (!$enlace || !$enlace->esValido()) {
            return view('catalogo.enlace_invalido');
        }
        
        // Registrar acceso
        $enlace->registrarAcceso();
        
        $cliente = $enlace->cliente;
        $categorias = Categoria::activas()->get();
        
        return view('catalogo.index_cliente', compact('enlace', 'cliente', 'categorias'));
    }
    
    /**
     * Flujo B: Acceso por vendedor (Tienda a Tienda)
     */
    public function index()
    {
        // Solo vendedores autenticados
        $this->middleware('auth');
        
        $user = Auth::user();
        
        // Si es vendedor, mostrar selector de clientes
        if ($user->hasRole('vendedor')) {
            $clientes = Cliente::where('vendedor_id', $user->id)
                              ->activos()
                              ->orderBy('nombre_contacto')
                              ->get();
                              
            return view('catalogo.seleccionar_cliente', compact('clientes'));
        }
        
        // Si es admin, puede ver todos los clientes
        if ($user->hasRole('admin')) {
            $clientes = Cliente::activos()
                              ->with('vendedor')
                              ->orderBy('nombre_contacto')
                              ->get();
                              
            return view('catalogo.seleccionar_cliente', compact('clientes'));
        }
        
        return redirect()->route('dashboard')->with('error', 'No tiene permisos para acceder al catálogo.');
    }
    
    /**
     * Flujo B: Mostrar catálogo para cliente seleccionado
     */
    public function mostrarParaCliente(Request $request)
    {
        $this->middleware('auth');
        
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id'
        ]);
        
        $user = Auth::user();
        $cliente = Cliente::findOrFail($request->cliente_id);
        
        // Verificar permisos
        if ($user->hasRole('vendedor') && $cliente->vendedor_id !== $user->id) {
            return redirect()->route('catalogo')
                           ->with('error', 'No tiene permisos para cotizar a este cliente.');
        }
        
        $categorias = Categoria::activas()->get();
        $enlace = null; // No hay enlace en el flujo B
        
        return view('catalogo.index', compact('cliente', 'categorias', 'enlace'));
    }
    
    /**
     * Obtener productos del catálogo (AJAX)
     */
    public function obtenerProductos(Request $request)
    {
        $query = Producto::activos()
            ->with([
                'imagenPrincipal', 
                'categoria',
                'stock' => function($q) {
                    $q->select('producto_id', 'variante_producto_id', 'cantidad_disponible', 'cantidad_reservada');
                },
                'variantes' => function($q) {
                    $q->activas()->with(['stock' => function($sq) {
                        $sq->select('producto_id', 'variante_producto_id', 'cantidad_disponible', 'cantidad_reservada');
                    }]);
                }
            ]);
        
        // Filtro por categoría
        if ($request->has('categoria_id') && $request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        // Búsqueda por nombre o referencia
        if ($request->has('busqueda') && $request->busqueda) {
            $query->buscar($request->busqueda);
        }
        
        $productos = $query->orderBy('nombre')->paginate(12);
        
        // Obtener configuración de visualización
        $listaPrecioId = null;
        $mostrarPrecios = false;
        $mostrarStock = false;
        
        if ($request->has('cliente_id')) {
            // Flujo B: Cliente seleccionado por vendedor
            $cliente = Cliente::find($request->cliente_id);
            if ($cliente) {
                $listaPrecioId = $cliente->lista_precio_id;
                $mostrarPrecios = true; // Siempre mostrar precios en flujo B
                $mostrarStock = true;   // Siempre mostrar stock en flujo B
            }
        } elseif ($request->has('enlace_token')) {
            // Flujo A: Acceso por token
            $enlace = EnlaceAcceso::where('token', $request->enlace_token)->first();
            if ($enlace && $enlace->esValido()) {
                $listaPrecioId = $enlace->cliente->lista_precio_id;
                $mostrarPrecios = $enlace->mostrar_precios;
                $mostrarStock = $enlace->mostrar_stock;
            }
        }
        
        // Agregar precios y stock a los productos
        foreach ($productos as $producto) {
            // Agregar precios
            if ($mostrarPrecios && $listaPrecioId) {
                $producto->precio = $producto->getPrecioPorLista($listaPrecioId);
            } else {
                $producto->precio = null;
            }
            
            // Agregar información de stock solo si se muestra Y se controla
            if ($mostrarStock) {
                $producto->stock_info = $this->obtenerStockProducto($producto);
            } else {
                $producto->stock_info = null;
            }
        }
        
        return response()->json([
            'productos' => $productos,
            'mostrar_precios' => $mostrarPrecios,
            'mostrar_stock' => $mostrarStock
        ]);
    }
    
    /**
     * Obtener detalle de producto con variantes (AJAX)
     */
    public function detalleProducto(Request $request, Producto $producto)
    {
        $producto->load([
            'variantes' => function($q) {
                $q->activas()->with(['stock' => function($sq) {
                    $sq->select('producto_id', 'variante_producto_id', 'cantidad_disponible', 'cantidad_reservada');
                }]);
            }, 
            'imagenes',
            'stock' => function($q) {
                $q->select('producto_id', 'variante_producto_id', 'cantidad_disponible', 'cantidad_reservada');
            }
        ]);
        
        // Obtener configuración según el contexto
        $listaPrecioId = null;
        $mostrarPrecios = false;
        $mostrarStock = false;
        
        if ($request->has('cliente_id')) {
            $cliente = Cliente::find($request->cliente_id);
            if ($cliente) {
                $listaPrecioId = $cliente->lista_precio_id;
                $mostrarPrecios = true;
                $mostrarStock = true;
            }
        } elseif ($request->has('enlace_token')) {
            $enlace = EnlaceAcceso::where('token', $request->enlace_token)->first();
            if ($enlace && $enlace->esValido()) {
                $listaPrecioId = $enlace->cliente->lista_precio_id;
                $mostrarPrecios = $enlace->mostrar_precios;
                $mostrarStock = $enlace->mostrar_stock;
            }
        }
        
        // Agregar precios y stock
        if ($mostrarPrecios && $listaPrecioId) {
            $producto->precio = $producto->getPrecioPorLista($listaPrecioId);
            
            // Precios de variantes
            foreach ($producto->variantes as $variante) {
                $variante->precio_final = $variante->getPrecioFinal($listaPrecioId);
            }
        }
        
        if ($mostrarStock) {
            $producto->stock_info = $this->obtenerStockProducto($producto);
            
            // Stock de variantes
            foreach ($producto->variantes as $variante) {
                $variante->stock_info = $this->obtenerStockVariante($producto, $variante);
            }
        }
        
        return response()->json([
            'producto' => $producto,
            'mostrar_precios' => $mostrarPrecios,
            'mostrar_stock' => $mostrarStock
        ]);
    }
    
    /**
     * Obtener información de stock de un producto
     */
    private function obtenerStockProducto($producto)
    {
        // Si no controla stock, siempre disponible
        if (!$producto->controlar_stock) {
            return [
                'tiene_stock' => true,
                'cantidad_disponible' => 999999,
                'estado' => 'disponible',
                'mensaje' => 'Disponible',
                'controla_stock' => false
            ];
        }

        if ($producto->tiene_variantes) {
            // Para productos con variantes, sumar el stock de todas las variantes
            $stockTotal = $producto->stock->sum(function($stock) {
                return $stock->cantidad_disponible - $stock->cantidad_reservada;
            });
            
            return [
                'tiene_stock' => $stockTotal > 0 || $producto->permitir_venta_sin_stock,
                'cantidad_disponible' => $stockTotal,
                'estado' => $this->getEstadoStock($stockTotal, false, $producto->permitir_venta_sin_stock),
                'mensaje' => $this->getMensajeStock($stockTotal, false, $producto->permitir_venta_sin_stock),
                'controla_stock' => true,
                'permite_sin_stock' => $producto->permitir_venta_sin_stock
            ];
        } else {
            // Para productos sin variantes
            $stock = $producto->stockPrincipal;
            if (!$stock) {
                return [
                    'tiene_stock' => $producto->permitir_venta_sin_stock,
                    'cantidad_disponible' => 0,
                    'estado' => $producto->permitir_venta_sin_stock ? 'sin_stock_permitido' : 'sin_stock',
                    'mensaje' => $producto->permitir_venta_sin_stock ? 'Sin stock (se permite venta)' : 'Sin stock',
                    'controla_stock' => true,
                    'permite_sin_stock' => $producto->permitir_venta_sin_stock
                ];
            }
            
            $disponible = $stock->cantidad_disponible - $stock->cantidad_reservada;
            
            return [
                'tiene_stock' => $disponible > 0 || $producto->permitir_venta_sin_stock,
                'cantidad_disponible' => $disponible,
                'stock_bajo' => $stock->stock_bajo,
                'estado' => $this->getEstadoStock($disponible, $stock->stock_bajo, $producto->permitir_venta_sin_stock),
                'mensaje' => $this->getMensajeStock($disponible, $stock->stock_bajo, $producto->permitir_venta_sin_stock),
                'controla_stock' => true,
                'permite_sin_stock' => $producto->permitir_venta_sin_stock
            ];
        }
    }
    
    /**
     * Obtener información de stock de una variante
     */
    private function obtenerStockVariante($producto, $variante)
    {
        // Si no controla stock, siempre disponible
        if (!$producto->controlar_stock) {
            return [
                'tiene_stock' => true,
                'cantidad_disponible' => 999999,
                'estado' => 'disponible',
                'mensaje' => 'Disponible',
                'controla_stock' => false
            ];
        }

        $stock = $variante->stock;
        if (!$stock) {
            return [
                'tiene_stock' => $producto->permitir_venta_sin_stock,
                'cantidad_disponible' => 0,
                'estado' => $producto->permitir_venta_sin_stock ? 'sin_stock_permitido' : 'sin_stock',
                'mensaje' => $producto->permitir_venta_sin_stock ? 'Sin stock (se permite venta)' : 'Sin stock',
                'controla_stock' => true,
                'permite_sin_stock' => $producto->permitir_venta_sin_stock
            ];
        }
        
        $disponible = $stock->cantidad_disponible - $stock->cantidad_reservada;
        
        return [
            'tiene_stock' => $disponible > 0 || $producto->permitir_venta_sin_stock,
            'cantidad_disponible' => $disponible,
            'stock_bajo' => $stock->stock_bajo,
            'estado' => $this->getEstadoStock($disponible, $stock->stock_bajo, $producto->permitir_venta_sin_stock),
            'mensaje' => $this->getMensajeStock($disponible, $stock->stock_bajo, $producto->permitir_venta_sin_stock),
            'controla_stock' => true,
            'permite_sin_stock' => $producto->permitir_venta_sin_stock
        ];
    }
    
    /**
     * Obtener estado de stock
     */
    private function getEstadoStock($cantidad, $stockBajo = false, $permiteSinStock = false)
    {
        if ($cantidad <= 0) {
            return $permiteSinStock ? 'sin_stock_permitido' : 'sin_stock';
        } elseif ($stockBajo) {
            return 'stock_bajo';
        } elseif ($cantidad <= 5) {
            return 'stock_limitado';
        } else {
            return 'disponible';
        }
    }
    
    /**
     * Obtener mensaje de stock
     */
    private function getMensajeStock($cantidad, $stockBajo = false, $permiteSinStock = false)
    {
        if ($cantidad <= 0) {
            return $permiteSinStock ? 'Sin stock (se permite venta)' : 'Sin stock';
        } elseif ($stockBajo) {
            return "Stock bajo ({$cantidad} disponibles)";
        } elseif ($cantidad <= 5) {
            return "Últimas {$cantidad} unidades";
        } else {
            return "{$cantidad} disponibles";
        }
    }
    
    /**
     * Verificar si se puede agregar al carrito
     */
    private function puedeAgregarAlCarrito($producto, $cantidad, $varianteId = null)
    {
        // Si no controla stock, siempre se puede agregar
        if (!$producto->controlar_stock) {
            return ['puede' => true, 'mensaje' => ''];
        }

        // Si permite venta sin stock, siempre se puede agregar
        if ($producto->permitir_venta_sin_stock) {
            return ['puede' => true, 'mensaje' => ''];
        }

        // Si controla stock y NO permite venta sin stock, verificar disponibilidad
        return [
            'puede' => $producto->hayStock($cantidad, $varianteId),
            'mensaje' => $producto->hayStock($cantidad, $varianteId) ? '' : 'Stock insuficiente'
        ];
    }
    
    /**
     * Guardar solicitud de cotización
     */
    public function guardarSolicitud(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.variante_id' => 'nullable|exists:variantes_productos,id',
            'notas_cliente' => 'nullable|string|max:1000'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Determinar cliente y enlace
            $cliente = null;
            $enlace = null;

            if ($request->input('enlace_token') !== null) {
                // Flujo A: Cliente con token
                $enlace = EnlaceAcceso::where('token', $request->enlace_token)->first();
                if (!$enlace || !$enlace->esValido()) {
                    throw new \Exception('El enlace de acceso no es válido.');
                }
                $cliente = $enlace->cliente;
            }
            elseif ($request->input('cliente_id') !== null) {
                // Flujo B: Vendedor
                $cliente = Cliente::findOrFail($request->cliente_id);

                // Verificar permisos
                if (Auth::user()->hasRole('vendedor') && $cliente->vendedor_id !== Auth::id()) {
                    throw new \Exception('No tiene permisos para crear solicitudes para este cliente.');
                }
            }
            else {
                throw new \Exception('No se pudo identificar el cliente.');
            }
            
            // Crear solicitud
            $solicitud = new SolicitudCotizacion([
                'cliente_id' => $cliente->id,
                'enlace_acceso_id' => $enlace ? $enlace->id : null,
                'estado' => 'pendiente',
                'notas_cliente' => $request->notas_cliente
            ]);
            $solicitud->save();
            
            // Obtener lista de precios
            $listaPrecioId = $cliente->lista_precio_id;
            $montoTotal = 0;
            
            // Agregar items y verificar stock SOLO si es necesario
            foreach ($request->items as $item) {
                $producto = Producto::with(['stockPrincipal', 'variantes.stock'])->findOrFail($item['producto_id']);
                
                // Verificar si se puede agregar al carrito
                $validacion = $this->puedeAgregarAlCarrito($producto, $item['cantidad'], $item['variante_id'] ?? null);
                if (!$validacion['puede']) {
                    throw new \Exception("Error con el producto {$producto->nombre}: {$validacion['mensaje']}");
                }
                
                // Determinar precio
                $precioUnitario = 0;
                $infoVariante = null;
                
                if (!empty($item['variante_id'])) {
                    // Producto con variante
                    $variante = $producto->variantes()->findOrFail($item['variante_id']);
                    $precioUnitario = $variante->getPrecioFinal($listaPrecioId) ?? 0;
                    $infoVariante = $variante->nombre_variante;
                } else {
                    // Producto sin variante
                    $precioUnitario = $producto->getPrecioPorLista($listaPrecioId) ?? 0;
                }
                
                $precioTotal = $precioUnitario * $item['cantidad'];
                $montoTotal += $precioTotal;
                
                // Crear item
                ItemSolicitudCotizacion::create([
                    'solicitud_cotizacion_id' => $solicitud->id,
                    'producto_id' => $producto->id,
                    'variante_producto_id' => $item['variante_id'] ?? null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'precio_total' => $precioTotal,
                    'referencia_producto' => $producto->referencia,
                    'nombre_producto' => $producto->nombre,
                    'info_variante' => $infoVariante
                ]);
            }
            
            // Actualizar monto total
            $solicitud->update(['monto_total' => $montoTotal]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'mensaje' => 'Solicitud de cotización creada exitosamente.',
                'numero_solicitud' => $solicitud->numero_solicitud
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al crear la solicitud: ' . $e->getMessage()
            ], 400);
        }
    }
}