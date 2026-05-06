<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Ubicacion;
use App\Services\MetricasProductosService;
use Illuminate\Http\Request;

class MetricasProductosController extends Controller
{
    public function __construct(private MetricasProductosService $svc)
    {
        $this->middleware('auth');
    }

    private function guardAdmin(): void
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }

    private function filtros(Request $r): array
    {
        $r->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'fuente'       => 'nullable|in:ambas,pdv,cotizaciones',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'ubicacion_id' => 'nullable|integer|exists:ubicaciones,id',
            'tipo'         => 'nullable|in:todos,con_ventas,sin_ventas,stock_bajo',
        ]);

        return $this->svc->normalizar([
            'fecha_inicio' => $r->input('fecha_inicio'),
            'fecha_fin'    => $r->input('fecha_fin'),
            'fuente'       => $r->input('fuente'),
            'categoria_id' => $r->filled('categoria_id') ? (int) $r->input('categoria_id') : null,
            'ubicacion_id' => $r->filled('ubicacion_id') ? (int) $r->input('ubicacion_id') : null,
            'tipo'         => $r->input('tipo'),
        ]);
    }

    public function index(Request $r)
    {
        $this->guardAdmin();
        $f = $this->filtros($r);

        if ($r->ajax()) {
            return $this->svc->datatable($f);
        }

        return view('metricas-productos.index', [
            'filtros'     => $f,
            'kpis'        => $this->svc->kpis($f),
            'categorias'  => Categoria::activas()->orderBy('nombre')->get(),
            'ubicaciones' => Ubicacion::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function kpis(Request $r)
    {
        $this->guardAdmin();
        return response()->json($this->svc->kpis($this->filtros($r)));
    }

    public function graficas(Request $r)
    {
        $this->guardAdmin();
        $f = $this->filtros($r);

        return view('metricas-productos.graficas', [
            'filtros'     => $f,
            'categorias'  => Categoria::activas()->orderBy('nombre')->get(),
            'ubicaciones' => Ubicacion::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function graficasData(Request $r)
    {
        $this->guardAdmin();
        return response()->json($this->svc->graficas($this->filtros($r)));
    }
}
