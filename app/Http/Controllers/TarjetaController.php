<?php

namespace App\Http\Controllers;

use App\Models\Tarjeta;
use App\Models\TableroColumna;
use App\Models\User;
use App\Services\TableroService;
use Illuminate\Http\Request;

class TarjetaController extends Controller
{
    protected TableroService $service;

    public function __construct(TableroService $service)
    {
        $this->service = $service;
    }

    public function show(Tarjeta $tarjeta)
    {
        $tarjeta->load([
            'columna.tablero',
            'usuarios',
            'etiquetas',
            'checklists.items.completadoPor',
            'comentarios.user',
            'adjuntos.user',
            'creador',
        ]);

        return response()->json([
            'tarjeta' => $tarjeta,
            'columna_nombre' => $tarjeta->columna->nombre,
            'tablero_id' => $tarjeta->columna->tablero_id,
            'estado_vencimiento' => $tarjeta->estado_vencimiento,
            'progreso_checklist' => $tarjeta->progreso_checklist,
            'html' => view('tableros.partials._card', ['tarjeta' => $tarjeta])->render(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'columna_id' => 'required|exists:tablero_columnas,id',
            'titulo' => 'required|string|max:500',
        ]);

        $maxPosicion = Tarjeta::where('columna_id', $request->columna_id)
            ->where('archivada', false)
            ->max('posicion') ?? -1;

        $tarjeta = Tarjeta::create([
            'columna_id' => $request->columna_id,
            'titulo' => $request->titulo,
            'posicion' => $maxPosicion + 1,
            'creado_por' => auth()->id(),
        ]);

        $tarjeta->load(['usuarios', 'etiquetas', 'checklists.items', 'comentarios', 'adjuntos']);

        $html = view('tableros.partials._card', [
            'tarjeta' => $tarjeta,
        ])->render();

        return response()->json([
            'success' => true,
            'tarjeta' => $tarjeta,
            'html' => $html,
        ]);
    }

    public function update(Request $request, Tarjeta $tarjeta)
    {
        $validated = $request->validate([
            'titulo' => 'nullable|string|max:500',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'fecha_completada' => 'nullable|date',
            'prioridad' => 'nullable|in:alta,media,baja',
            'color_portada' => 'nullable|string|max:7',
        ]);

        // Allow explicitly setting fields to null (e.g., clearing dates/colors)
        $updateData = [];
        foreach ($validated as $key => $value) {
            if ($request->has($key)) {
                $updateData[$key] = $value === '' ? null : $value;
            }
        }
        $tarjeta->update($updateData);

        $tarjeta->load(['usuarios', 'etiquetas', 'checklists.items', 'comentarios', 'adjuntos']);

        return response()->json([
            'success' => true,
            'tarjeta' => $tarjeta,
            'estado_vencimiento' => $tarjeta->estado_vencimiento,
            'progreso_checklist' => $tarjeta->progreso_checklist,
            'html' => view('tableros.partials._card', ['tarjeta' => $tarjeta])->render(),
        ]);
    }

    public function destroy(Tarjeta $tarjeta)
    {
        $tarjeta->delete();
        return response()->json(['success' => true]);
    }

    public function mover(Request $request, Tarjeta $tarjeta)
    {
        $request->validate([
            'columna_id' => 'required|exists:tablero_columnas,id',
            'posicion' => 'required|integer|min:0',
        ]);

        $this->service->moverTarjeta($tarjeta, $request->columna_id, $request->posicion, auth()->user());

        return response()->json(['success' => true]);
    }

    public function reordenar(Request $request)
    {
        $request->validate(['posiciones' => 'required|array']);
        $this->service->reordenarTarjetas($request->posiciones);

        return response()->json(['success' => true]);
    }

    public function asignarUsuario(Request $request, Tarjeta $tarjeta)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        if ($tarjeta->usuarios()->where('users.id', $request->user_id)->exists()) {
            return response()->json(['error' => 'Usuario ya asignado'], 422);
        }

        $tarjeta->usuarios()->attach($request->user_id);
        $user = User::find($request->user_id);

        $this->service->registrarActividad($tarjeta, auth()->user(), "asignó a {$user->name}");

        return response()->json([
            'success' => true,
            'user' => $user->only('id', 'name', 'profile_photo'),
        ]);
    }

    public function desasignarUsuario(Tarjeta $tarjeta, User $user)
    {
        $tarjeta->usuarios()->detach($user->id);
        $this->service->registrarActividad($tarjeta, auth()->user(), "desasignó a {$user->name}");

        return response()->json(['success' => true]);
    }

    public function toggleEtiqueta(Request $request, Tarjeta $tarjeta)
    {
        $request->validate(['etiqueta_id' => 'required|exists:tablero_etiquetas,id']);

        $attached = $tarjeta->etiquetas()->toggle($request->etiqueta_id);

        return response()->json([
            'success' => true,
            'attached' => !empty($attached['attached']),
        ]);
    }

    public function archivar(Tarjeta $tarjeta)
    {
        $tarjeta->update(['archivada' => !$tarjeta->archivada]);
        return response()->json(['success' => true, 'archivada' => $tarjeta->archivada]);
    }
}
