<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroMercadoRequest;
use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use App\Models\TipoProductoMercado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistroMercadoController extends Controller
{
    public function index(Request $request): View
    {
        $tipoId = $request->integer('tipo_id') ?: null;

        $productos = ProductoMercado::activos()
            ->with('tipo')
            ->when($tipoId, fn ($q) => $q->where('tipo_id', $tipoId))
            ->orderBy('nombre')
            ->get();

        $tipos = TipoProductoMercado::orderBy('nombre')->get();

        return view('registro-mercado.index', compact('productos', 'tipos', 'tipoId'));
    }

    public function create(Request $request, ProductoMercado $producto): View
    {
        abort_unless($producto->activo, 404);

        $producto->load('tipo');
        $tipoId = $request->integer('tipo_id') ?: null;

        return view('registro-mercado.create', compact('producto', 'tipoId'));
    }

    public function store(StoreRegistroMercadoRequest $request): RedirectResponse
    {
        RegistroMercado::create($request->safe()->only(['producto_mercado_id', 'cantidad', 'valor']));

        $tipoId = $request->integer('tipo_id') ?: null;

        return redirect()
            ->route('registro-mercado.index', $tipoId ? ['tipo_id' => $tipoId] : [])
            ->with('success', 'Registro guardado.');
    }
}
