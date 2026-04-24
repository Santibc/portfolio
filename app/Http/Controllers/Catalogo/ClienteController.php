<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogo\ClienteRequest;
use App\Models\Cliente;
use App\Models\Incoterm;
use App\Models\Moneda;
use App\Models\PlantillaFactura;
use App\Models\Puerto;
use App\Models\TipoPago;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cliente::with(['monedaPreferida', 'incoterm'])->orderBy('nombre');

        $buscar = $request->string('q')->toString();
        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('identificacion', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        $clientes = $query->paginate(20)->withQueryString();

        return view('catalogo.clientes.index', [
            'clientes' => $clientes,
            'buscar' => $buscar,
            'tipo' => $request->input('tipo', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalogo.clientes.form', $this->formData(new Cliente));
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        Cliente::create($request->validated());

        return redirect()->route('catalogos.clientes.index')->with('success', 'Cliente creado.');
    }

    public function edit(Cliente $cliente): View
    {
        return view('catalogo.clientes.form', $this->formData($cliente));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()->route('catalogos.clientes.index')->with('success', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()->route('catalogos.clientes.index')->with('success', 'Cliente eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Cliente $cliente): array
    {
        return [
            'cliente' => $cliente,
            'monedas' => Moneda::activas()->orderBy('codigo')->get(),
            'incoterms' => Incoterm::where('activo', true)->orderBy('codigo')->get(),
            'puertos' => Puerto::where('activo', true)->orderBy('nombre')->get(),
            'tiposPago' => TipoPago::where('activo', true)->orderBy('dias_credito')->get(),
            'plantillas' => PlantillaFactura::activas()->orderByDesc('es_default')->orderBy('nombre')->get(),
        ];
    }
}
