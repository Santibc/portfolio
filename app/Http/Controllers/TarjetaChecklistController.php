<?php

namespace App\Http\Controllers;

use App\Models\Tarjeta;
use App\Models\TarjetaChecklist;
use App\Models\TarjetaChecklistItem;
use Illuminate\Http\Request;

class TarjetaChecklistController extends Controller
{
    public function store(Request $request, Tarjeta $tarjeta)
    {
        $request->validate(['titulo' => 'required|string|max:255']);

        $maxPosicion = $tarjeta->checklists()->max('posicion') ?? -1;

        $checklist = $tarjeta->checklists()->create([
            'titulo' => $request->titulo,
            'posicion' => $maxPosicion + 1,
        ]);

        return response()->json([
            'success' => true,
            'checklist' => $checklist->load('items'),
        ]);
    }

    public function update(Request $request, TarjetaChecklist $checklist)
    {
        $request->validate(['titulo' => 'required|string|max:255']);
        $checklist->update(['titulo' => $request->titulo]);

        return response()->json(['success' => true]);
    }

    public function destroy(TarjetaChecklist $checklist)
    {
        $checklist->delete();
        return response()->json(['success' => true]);
    }

    public function storeItem(Request $request, TarjetaChecklist $checklist)
    {
        $request->validate(['texto' => 'required|string|max:500']);

        $maxPosicion = $checklist->items()->max('posicion') ?? -1;

        $item = $checklist->items()->create([
            'texto' => $request->texto,
            'posicion' => $maxPosicion + 1,
        ]);

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    public function toggleItem(TarjetaChecklistItem $item)
    {
        $item->update([
            'completado' => !$item->completado,
            'completado_por' => !$item->completado ? auth()->id() : null,
            'fecha_completado' => !$item->completado ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'completado' => $item->completado,
        ]);
    }

    public function destroyItem(TarjetaChecklistItem $item)
    {
        $item->delete();
        return response()->json(['success' => true]);
    }
}
