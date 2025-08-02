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
        $query = Producto::activos()->with(['imagenPrincipal', 'categoria']);
        
        // Filtro por categoría
        if ($request->has('categoria_id') && $request->categoria_id) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        // Búsqueda por nombre o referencia
        if ($request->has('busqueda') && $request->busqueda) {
            $query->buscar($request->busqueda);
        }
        
        $productos = $query->orderBy('nombre')->paginate(12);
        
        // Obtener lista de precios del cliente
        $listaPrecioId = null;
        $mostrarPrecios = false;
        
        if ($request->has('cliente_id')) {
            // Flujo B: Cliente seleccionado por vendedor
            $cliente = Cliente::find($request->cliente_id);
            if ($cliente) {
                $listaPrecioId = $cliente->lista_precio_id;
                $mostrarPrecios = true; // Siempre mostrar precios en flujo B
            }
        } elseif ($request->has('enlace_token')) {
            // Flujo A: Acceso por token
            $enlace = EnlaceAcceso::where('token', $request->enlace_token)->first();
            if ($enlace && $enlace->esValido()) {
                $listaPrecioId = $enlace->cliente->lista_precio_id;
                $mostrarPrecios = $enlace->mostrar_precios;
            }
        }
        
        // Agregar precios a los productos
        foreach ($productos as $producto) {
            if ($mostrarPrecios && $listaPrecioId) {
                $producto->precio = $producto->getPrecioPorLista($listaPrecioId);
            } else {
                $producto->precio = null;
            }
        }
        
        return response()->json([
            'productos' => $productos,
            'mostrar_precios' => $mostrarPrecios
        ]);
    }
    
    /**
     * Obtener detalle de producto con variantes (AJAX)
     */
    public function detalleProducto(Request $request, Producto $producto)
    {
        $producto->load(['variantes' => function($q) {
            $q->activas();
        }, 'imagenes']);
        
        // Obtener precio según el contexto
        $listaPrecioId = null;
        $mostrarPrecios = false;
        
        if ($request->has('cliente_id')) {
            $cliente = Cliente::find($request->cliente_id);
            if ($cliente) {
                $listaPrecioId = $cliente->lista_precio_id;
                $mostrarPrecios = true;
            }
        } elseif ($request->has('enlace_token')) {
            $enlace = EnlaceAcceso::where('token', $request->enlace_token)->first();
            if ($enlace && $enlace->esValido()) {
                $listaPrecioId = $enlace->cliente->lista_precio_id;
                $mostrarPrecios = $enlace->mostrar_precios;
            }
        }
        
        // Agregar precios
        if ($mostrarPrecios && $listaPrecioId) {
            $producto->precio = $producto->getPrecioPorLista($listaPrecioId);
            
            // Precios de variantes
            foreach ($producto->variantes as $variante) {
                $variante->precio_final = $variante->getPrecioFinal($listaPrecioId);
            }
        }
        
        return response()->json([
            'producto' => $producto,
            'mostrar_precios' => $mostrarPrecios
        ]);
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
            
            // Agregar items
            foreach ($request->items as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                
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