<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroMercadoRequest;
use App\Models\MetodoPago;
use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use App\Models\TipoProductoMercado;
use App\Services\TurnoCajaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistroMercadoController extends Controller
{
    public function __construct(private TurnoCajaService $turnos) {}

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

        $metodos = MetodoPago::activos()->orderBy('orden')->orderBy('nombre')->get();
        $turnoActivo = $this->turnos->turnoActivo();

        return view('registro-mercado.create', compact('producto', 'tipoId', 'metodos', 'turnoActivo'));
    }

    public function store(StoreRegistroMercadoRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['producto_mercado_id', 'cantidad', 'valor', 'metodo_pago_id', 'observacion']);

        if ($request->boolean('vincular_caja')) {
            $data['turno_caja_id'] = $this->turnos->turnoActivo()?->id;
        }

        RegistroMercado::create($data);

        $tipoId = $request->integer('tipo_id') ?: null;

        return redirect()
            ->route('registro-mercado.index', $tipoId ? ['tipo_id' => $tipoId] : [])
            ->with('success', 'Registro guardado.');
    }
}
