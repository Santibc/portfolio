<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Carrito;
use App\Models\ListaPrecio;
use Illuminate\Support\Facades\Session;

class TiendaPublicaController extends Controller
{
    /**
     * Obtener carrito de la sesión
     */
    private function obtenerCarrito($empresaId)
    {
        $sessionId = Session::getId();
        return Carrito::obtenerOCrear($sessionId, $empresaId);
    }
    /**
     * Mostrar catálogo público de productos
     */
    public function catalogo(Request $request)
    {
        $empresa = Empresa::first();

        // Obtener categorías activas
        $categorias = Categoria::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        // Query de productos
        $query = Producto::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->where('eliminado', false)
            ->with(['categoria', 'imagenPrincipal', 'precios.listaPrecio']);

        // Filtro por categoría
        if ($request->has('categoria') && $request->categoria) {
            $query->where('categoria_id', $request->categoria);
        }

        // Búsqueda por nombre
        if ($request->has('busqueda') && $request->busqueda) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->busqueda . '%')
                  ->orWhere('descripcion', 'LIKE', '%' . $request->busqueda . '%');
            });
        }

        $productos = $query->orderBy('nombre')->paginate(12);

        $carrito = $this->obtenerCarrito($empresa->id);
        $listaPrecio = ListaPrecio::where('activo', true)->first();

        return view('tienda.catalogo', compact('productos', 'categorias', 'empresa', 'carrito', 'listaPrecio'));
    }

    /**
     * Mostrar detalle de producto
     */
    public function producto($id)
    {
        $empresa = Empresa::first();

        $producto = Producto::where('empresa_id', $empresa->id)
            ->where('activo', true)
            ->where('eliminado', false)
            ->with([
                'categoria',
                'imagenes' => function($q) {
                    $q->orderBy('orden');
                },
                'variantes' => function($q) {
                    $q->where('activo', true)
                      ->with(['preciosVariante.listaPrecio', 'stock']);
                },
                'precios.listaPrecio',
                'stock'
            ])
            ->findOrFail($id);

        // Productos relacionados de la misma categoría
        $relacionados = Producto::where('empresa_id', $empresa->id)
            ->where('categoria_id', $producto->categoria_id)
            ->where('id', '!=', $producto->id)
            ->where('activo', true)
            ->where('eliminado', false)
            ->with(['imagenPrincipal', 'precios.listaPrecio'])
            ->limit(4)
            ->get();

        $carrito = $this->obtenerCarrito($empresa->id);
        $listaPrecio = ListaPrecio::where('activo', true)->first();

        return view('tienda.producto', compact('producto', 'relacionados', 'empresa', 'carrito', 'listaPrecio'));
    }
}
