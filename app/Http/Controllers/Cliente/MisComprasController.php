<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\ItemCompra;
use App\Models\CalificacionProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisComprasController extends Controller
{
    /**
     * Mostrar lista de compras del cliente
     */
    public function index()
    {
        $user = Auth::user();

        // Obtener compras del usuario por user_id o email
        $compras = Compra::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('email_cliente', $user->email);
        })
        ->with(['items.producto', 'ciudad', 'empresa'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('cliente.mis-compras.index', compact('compras'));
    }

    /**
     * Mostrar detalle de una compra
     */
    public function show($id)
    {
        $user = Auth::user();

        $compra = Compra::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('email_cliente', $user->email);
        })
        ->with(['items.producto.imagenPrincipal', 'items.variante', 'ciudad', 'empresa', 'envio'])
        ->findOrFail($id);

        // Obtener calificaciones existentes del usuario para esta compra
        $calificacionesExistentes = CalificacionProducto::where('compra_id', $compra->id)
            ->where('user_id', $user->id)
            ->pluck('item_compra_id')
            ->toArray();

        return view('cliente.mis-compras.show', compact('compra', 'calificacionesExistentes'));
    }

    /**
     * Mostrar formulario para calificar un item de compra
     */
    public function calificar($itemCompraId)
    {
        $user = Auth::user();

        $itemCompra = ItemCompra::with(['compra', 'producto.imagenPrincipal', 'variante'])
            ->findOrFail($itemCompraId);

        // Verificar que el item pertenece a una compra del usuario
        $compra = $itemCompra->compra;
        if ($compra->user_id !== $user->id && $compra->email_cliente !== $user->email) {
            abort(403, 'No tienes permiso para calificar este producto.');
        }

        // Verificar que la compra puede ser calificada
        if (!$compra->puedeSerCalificada()) {
            return redirect()->route('cliente.compras.show', $compra->id)
                ->with('error', 'Esta compra aún no puede ser calificada. Debe estar en estado pagada, enviada o entregada.');
        }

        // Verificar si ya existe una calificación
        $calificacionExistente = CalificacionProducto::where('user_id', $user->id)
            ->where('item_compra_id', $itemCompraId)
            ->first();

        if ($calificacionExistente) {
            return redirect()->route('cliente.compras.show', $compra->id)
                ->with('info', 'Ya has calificado este producto.');
        }

        return view('cliente.calificar', compact('itemCompra', 'compra'));
    }

    /**
     * Guardar calificación
     */
    public function guardarCalificacion(Request $request)
    {
        $request->validate([
            'item_compra_id' => 'required|exists:items_compra,id',
            'estrellas' => 'required|integer|min:1|max:5',
            'titulo' => 'nullable|string|max:255',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        $itemCompra = ItemCompra::with('compra')->findOrFail($request->item_compra_id);
        $compra = $itemCompra->compra;

        // Verificar que el item pertenece a una compra del usuario
        if ($compra->user_id !== $user->id && $compra->email_cliente !== $user->email) {
            abort(403, 'No tienes permiso para calificar este producto.');
        }

        // Verificar que la compra puede ser calificada
        if (!$compra->puedeSerCalificada()) {
            return redirect()->route('cliente.compras.show', $compra->id)
                ->with('error', 'Esta compra aún no puede ser calificada.');
        }

        // Verificar si ya existe una calificación
        $calificacionExistente = CalificacionProducto::where('user_id', $user->id)
            ->where('item_compra_id', $request->item_compra_id)
            ->first();

        if ($calificacionExistente) {
            return redirect()->route('cliente.compras.show', $compra->id)
                ->with('info', 'Ya has calificado este producto.');
        }

        // Crear la calificación
        CalificacionProducto::create([
            'producto_id' => $itemCompra->producto_id,
            'user_id' => $user->id,
            'compra_id' => $compra->id,
            'item_compra_id' => $itemCompra->id,
            'estrellas' => $request->estrellas,
            'titulo' => $request->titulo,
            'comentario' => $request->comentario,
            'verificada' => true,
            'aprobada' => true,
        ]);

        return redirect()->route('cliente.compras.show', $compra->id)
            ->with('success', '¡Gracias por tu calificación!');
    }
}
