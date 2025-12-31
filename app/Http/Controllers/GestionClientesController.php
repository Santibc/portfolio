<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DireccionCliente;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestionClientesController extends Controller
{
    /**
     * Lista de clientes (agrupados por email)
     */
    public function index(Request $request)
    {
        $empresa = auth()->user()->empresa;

        // Obtener clientes únicos desde las compras
        $query = Compra::where('empresa_id', $empresa->id)
            ->select(
                'email_cliente',
                'nombre_cliente',
                'telefono_cliente',
                DB::raw('COUNT(*) as total_compras'),
                DB::raw('SUM(total) as total_gastado'),
                DB::raw('MAX(created_at) as ultima_compra'),
                DB::raw('MIN(created_at) as primera_compra')
            )
            ->groupBy('email_cliente', 'nombre_cliente', 'telefono_cliente');

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('email_cliente', 'like', "%{$buscar}%")
                  ->orWhere('nombre_cliente', 'like', "%{$buscar}%")
                  ->orWhere('telefono_cliente', 'like', "%{$buscar}%");
            });
        }

        // Ordenamiento
        $ordenar = $request->get('ordenar', 'ultima_compra');
        $direccion = $request->get('direccion', 'desc');

        switch ($ordenar) {
            case 'total_gastado':
                $query->orderByRaw('SUM(total) ' . $direccion);
                break;
            case 'total_compras':
                $query->orderByRaw('COUNT(*) ' . $direccion);
                break;
            default:
                $query->orderByRaw('MAX(created_at) ' . $direccion);
        }

        $clientes = $query->paginate(20)->withQueryString();

        // Estadísticas generales
        $estadisticas = [
            'total_clientes' => Compra::where('empresa_id', $empresa->id)
                ->distinct('email_cliente')
                ->count('email_cliente'),
            'clientes_recurrentes' => Compra::where('empresa_id', $empresa->id)
                ->select('email_cliente')
                ->groupBy('email_cliente')
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->count(),
            'ticket_promedio' => Compra::where('empresa_id', $empresa->id)
                ->where('estado', 'pagada')
                ->avg('total') ?? 0,
            'nuevos_mes' => Compra::where('empresa_id', $empresa->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->distinct('email_cliente')
                ->count('email_cliente')
        ];

        return view('gestion-clientes.index', compact('clientes', 'estadisticas'));
    }

    /**
     * Ver detalle de un cliente (historial, direcciones)
     */
    public function show($email)
    {
        $empresa = auth()->user()->empresa;

        // Verificar que el cliente tiene compras en esta empresa
        $primeraCompra = Compra::where('empresa_id', $empresa->id)
            ->where('email_cliente', $email)
            ->first();

        if (!$primeraCompra) {
            abort(404, 'Cliente no encontrado');
        }

        // Datos del cliente
        $cliente = Compra::where('empresa_id', $empresa->id)
            ->where('email_cliente', $email)
            ->select(
                'email_cliente',
                'nombre_cliente',
                'telefono_cliente',
                DB::raw('COUNT(*) as total_compras'),
                DB::raw('SUM(total) as total_gastado'),
                DB::raw('AVG(total) as ticket_promedio'),
                DB::raw('MAX(created_at) as ultima_compra'),
                DB::raw('MIN(created_at) as primera_compra')
            )
            ->groupBy('email_cliente', 'nombre_cliente', 'telefono_cliente')
            ->first();

        // Historial de compras
        $compras = Compra::where('empresa_id', $empresa->id)
            ->where('email_cliente', $email)
            ->with(['items.producto', 'ciudad'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Direcciones guardadas
        $direcciones = DireccionCliente::where('email_cliente', $email)
            ->with('ciudad.departamento')
            ->orderBy('es_predeterminada', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Productos más comprados por este cliente
        $productosFavoritos = DB::table('items_compra')
            ->join('compras', 'items_compra.compra_id', '=', 'compras.id')
            ->join('productos', 'items_compra.producto_id', '=', 'productos.id')
            ->where('compras.empresa_id', $empresa->id)
            ->where('compras.email_cliente', $email)
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.referencia',
                DB::raw('SUM(items_compra.cantidad) as cantidad_total'),
                DB::raw('COUNT(*) as veces_comprado')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.referencia')
            ->orderByDesc('cantidad_total')
            ->limit(5)
            ->get();

        // Ciudades para el selector de direcciones
        $ciudades = Ciudad::with('departamento')
            ->orderBy('nombre')
            ->get()
            ->groupBy('departamento.nombre');

        return view('gestion-clientes.show', compact(
            'cliente',
            'compras',
            'direcciones',
            'productosFavoritos',
            'ciudades'
        ));
    }

    /**
     * Guardar una nueva dirección para el cliente
     */
    public function guardarDireccion(Request $request)
    {
        $request->validate([
            'email_cliente' => 'required|email',
            'alias' => 'nullable|string|max:50',
            'nombre_destinatario' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'ciudad_id' => 'required|exists:ciudades,id',
            'instrucciones' => 'nullable|string'
        ]);

        // Si es predeterminada, quitar el flag de las demás
        if ($request->es_predeterminada) {
            DireccionCliente::where('email_cliente', $request->email_cliente)
                ->update(['es_predeterminada' => false]);
        }

        DireccionCliente::create([
            'email_cliente' => $request->email_cliente,
            'alias' => $request->alias,
            'nombre_destinatario' => $request->nombre_destinatario,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad_id' => $request->ciudad_id,
            'instrucciones' => $request->instrucciones,
            'es_predeterminada' => $request->boolean('es_predeterminada')
        ]);

        return back()->with('success', 'Dirección guardada correctamente');
    }

    /**
     * Actualizar una dirección
     */
    public function actualizarDireccion(Request $request, DireccionCliente $direccion)
    {
        $request->validate([
            'alias' => 'nullable|string|max:50',
            'nombre_destinatario' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'ciudad_id' => 'required|exists:ciudades,id',
            'instrucciones' => 'nullable|string'
        ]);

        // Si es predeterminada, quitar el flag de las demás
        if ($request->es_predeterminada) {
            DireccionCliente::where('email_cliente', $direccion->email_cliente)
                ->where('id', '!=', $direccion->id)
                ->update(['es_predeterminada' => false]);
        }

        $direccion->update([
            'alias' => $request->alias,
            'nombre_destinatario' => $request->nombre_destinatario,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'ciudad_id' => $request->ciudad_id,
            'instrucciones' => $request->instrucciones,
            'es_predeterminada' => $request->boolean('es_predeterminada')
        ]);

        return back()->with('success', 'Dirección actualizada correctamente');
    }

    /**
     * Eliminar una dirección
     */
    public function eliminarDireccion(DireccionCliente $direccion)
    {
        $direccion->delete();

        return back()->with('success', 'Dirección eliminada correctamente');
    }

    /**
     * API: Obtener direcciones de un cliente
     */
    public function obtenerDirecciones($email)
    {
        $direcciones = DireccionCliente::where('email_cliente', $email)
            ->with('ciudad.departamento')
            ->orderBy('es_predeterminada', 'desc')
            ->get();

        return response()->json($direcciones);
    }
}
