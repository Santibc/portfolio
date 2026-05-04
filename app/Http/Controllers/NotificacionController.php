<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $notificaciones = Notificacion::where('usuario_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas' => $notificaciones->where('leida', false)->count(),
        ]);
    }

    public function destroy($id)
    {
        $notificacion = Notificacion::where('id', $id)
            ->where('usuario_id', auth()->id())
            ->firstOrFail();

        $notificacion->delete();

        return response()->json(['success' => true]);
    }

    public function marcarLeidas()
    {
        Notificacion::where('usuario_id', auth()->id())
            ->where('leida', false)
            ->update(['leida' => true, 'leida_en' => now()]);

        return response()->json(['success' => true]);
    }

    public function eliminarTodas()
    {
        Notificacion::where('usuario_id', auth()->id())->delete();

        return response()->json(['success' => true]);
    }
}
