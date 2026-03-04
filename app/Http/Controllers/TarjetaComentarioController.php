<?php

namespace App\Http\Controllers;

use App\Models\Tarjeta;
use App\Models\TarjetaComentario;
use Illuminate\Http\Request;

class TarjetaComentarioController extends Controller
{
    public function index(Tarjeta $tarjeta)
    {
        $comentarios = $tarjeta->comentarios()
            ->with('user:id,name,profile_photo')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($comentarios);
    }

    public function store(Request $request, Tarjeta $tarjeta)
    {
        $request->validate(['contenido' => 'required|string']);

        $comentario = $tarjeta->comentarios()->create([
            'user_id' => auth()->id(),
            'contenido' => $request->contenido,
            'tipo' => 'comentario',
        ]);

        $comentario->load('user:id,name,profile_photo');

        return response()->json([
            'success' => true,
            'comentario' => $comentario,
        ]);
    }

    public function destroy(TarjetaComentario $comentario)
    {
        if ($comentario->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comentario->delete();
        return response()->json(['success' => true]);
    }
}
