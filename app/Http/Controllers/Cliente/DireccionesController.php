<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\DireccionCliente;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionesController extends Controller
{
    public function index()
    {
        $direcciones = DireccionCliente::porCliente(Auth::user()->email)
            ->with('ciudad')
            ->orderBy('es_predeterminada', 'desc')
            ->get();

        $ciudades = Ciudad::orderBy('nombre')->get();

        return view('cliente.direcciones.index', compact('direcciones', 'ciudades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alias' => ['nullable', 'string', 'max:50'],
            'nombre_destinatario' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['required', 'string', 'max:500'],
            'ciudad_id' => ['required', 'exists:ciudades,id'],
            'instrucciones' => ['nullable', 'string', 'max:500'],
            'es_predeterminada' => ['boolean'],
        ]);

        // Si es predeterminada, quitar de las demás
        if ($request->boolean('es_predeterminada')) {
            DireccionCliente::porCliente(Auth::user()->email)
                ->update(['es_predeterminada' => false]);
        }

        DireccionCliente::create([
            ...$validated,
            'email_cliente' => Auth::user()->email,
        ]);

        return redirect()->route('cliente.direcciones')
            ->with('success', 'Dirección agregada correctamente.');
    }

    public function update(Request $request, DireccionCliente $direccion)
    {
        // Verificar que la dirección pertenece al usuario
        if ($direccion->email_cliente !== Auth::user()->email) {
            abort(403);
        }

        $validated = $request->validate([
            'alias' => ['nullable', 'string', 'max:50'],
            'nombre_destinatario' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['required', 'string', 'max:500'],
            'ciudad_id' => ['required', 'exists:ciudades,id'],
            'instrucciones' => ['nullable', 'string', 'max:500'],
            'es_predeterminada' => ['boolean'],
        ]);

        // Si es predeterminada, quitar de las demás
        if ($request->boolean('es_predeterminada')) {
            DireccionCliente::porCliente(Auth::user()->email)
                ->where('id', '!=', $direccion->id)
                ->update(['es_predeterminada' => false]);
        }

        $direccion->update($validated);

        return redirect()->route('cliente.direcciones')
            ->with('success', 'Dirección actualizada correctamente.');
    }

    public function destroy(DireccionCliente $direccion)
    {
        // Verificar que la dirección pertenece al usuario
        if ($direccion->email_cliente !== Auth::user()->email) {
            abort(403);
        }

        $direccion->delete();

        return redirect()->route('cliente.direcciones')
            ->with('success', 'Dirección eliminada correctamente.');
    }

    public function setPredeterminada(DireccionCliente $direccion)
    {
        // Verificar que la dirección pertenece al usuario
        if ($direccion->email_cliente !== Auth::user()->email) {
            abort(403);
        }

        DireccionCliente::porCliente(Auth::user()->email)
            ->update(['es_predeterminada' => false]);

        $direccion->update(['es_predeterminada' => true]);

        return redirect()->route('cliente.direcciones')
            ->with('success', 'Dirección marcada como predeterminada.');
    }
}
