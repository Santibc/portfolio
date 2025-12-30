<?php

namespace App\Http\Controllers;

use App\Models\Repartidor;
use Illuminate\Http\Request;

class RepartidoresController extends Controller
{
    public function index()
    {
        $empresa = auth()->user()->empresa;

        $repartidores = Repartidor::where('empresa_id', $empresa->id)
            ->withCount(['compras as entregas_pendientes' => function ($query) {
                $query->whereIn('estado', ['pagada', 'enviada']);
            }])
            ->withCount(['compras as entregas_hoy' => function ($query) {
                $query->whereDate('fecha_entrega_deseada', today());
            }])
            ->orderBy('nombre')
            ->get();

        return view('repartidores.index', compact('repartidores'));
    }

    public function create()
    {
        return view('repartidores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'documento' => 'nullable|string|max:50',
            'vehiculo' => 'nullable|string|max:50',
            'placa' => 'nullable|string|max:20',
            'notas' => 'nullable|string'
        ]);

        $empresa = auth()->user()->empresa;

        Repartidor::create([
            'empresa_id' => $empresa->id,
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'documento' => $request->documento,
            'vehiculo' => $request->vehiculo,
            'placa' => $request->placa,
            'notas' => $request->notas,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route('repartidores.index')
            ->with('success', 'Repartidor creado correctamente');
    }

    public function edit(Repartidor $repartidor)
    {
        $empresa = auth()->user()->empresa;

        if ($repartidor->empresa_id !== $empresa->id) {
            abort(403);
        }

        return view('repartidores.edit', compact('repartidor'));
    }

    public function update(Request $request, Repartidor $repartidor)
    {
        $empresa = auth()->user()->empresa;

        if ($repartidor->empresa_id !== $empresa->id) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'documento' => 'nullable|string|max:50',
            'vehiculo' => 'nullable|string|max:50',
            'placa' => 'nullable|string|max:20',
            'notas' => 'nullable|string'
        ]);

        $repartidor->update([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'documento' => $request->documento,
            'vehiculo' => $request->vehiculo,
            'placa' => $request->placa,
            'notas' => $request->notas,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route('repartidores.index')
            ->with('success', 'Repartidor actualizado correctamente');
    }

    public function destroy(Repartidor $repartidor)
    {
        $empresa = auth()->user()->empresa;

        if ($repartidor->empresa_id !== $empresa->id) {
            abort(403);
        }

        // Verificar si tiene entregas pendientes
        $entregasPendientes = $repartidor->compras()
            ->whereIn('estado', ['pagada', 'enviada'])
            ->count();

        if ($entregasPendientes > 0) {
            return back()->with('error', 'No se puede eliminar el repartidor porque tiene entregas pendientes.');
        }

        $repartidor->delete();

        return redirect()->route('repartidores.index')
            ->with('success', 'Repartidor eliminado correctamente');
    }

    public function toggleActivo(Repartidor $repartidor)
    {
        $empresa = auth()->user()->empresa;

        if ($repartidor->empresa_id !== $empresa->id) {
            abort(403);
        }

        $repartidor->update(['activo' => !$repartidor->activo]);

        return response()->json([
            'success' => true,
            'activo' => $repartidor->activo,
            'message' => $repartidor->activo ? 'Repartidor activado' : 'Repartidor desactivado'
        ]);
    }
}
