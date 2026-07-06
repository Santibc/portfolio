<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRegistroMercadoRequest;
use App\Models\ProductoMercado;
use App\Models\RegistroMercado;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardMercadoController extends Controller
{
    public function index(Request $request): View
    {
        $hoy = today()->toDateString();
        $desde = $request->input('desde', $hoy);
        $hasta = $request->input('hasta', $hoy);

        $registros = RegistroMercado::with(['producto.tipo', 'metodoPago'])
            ->enRangoFechas($desde, $hasta)
            ->latest()
            ->get();

        $totalGastado = (int) $registros->sum('valor');
        $totalCantidad = (float) $registros->sum('cantidad');
        $productosDistintos = $registros->pluck('producto_mercado_id')->unique()->count();
        $promedioPorRegistro = $registros->isEmpty() ? 0 : (int) round($totalGastado / $registros->count());

        $totalesPorMetodo = $registros
            ->groupBy(fn (RegistroMercado $r) => $r->metodoPago?->nombre ?? 'Sin método')
            ->map(fn ($grupo, $nombre) => [
                'nombre' => $nombre,
                'total'  => (int) $grupo->sum('valor'),
                'count'  => $grupo->count(),
            ])
            ->sortByDesc('total')
            ->values();

        $rows = $registros->map(function (RegistroMercado $r) {
            $thumb = $r->producto?->hasImagen()
                ? '<img src="' . e($r->producto->imagen_url) . '" alt="' . e($r->producto->nombre) . '" class="w-9 h-9 rounded-lg object-cover border border-cream-200 dark:border-cream-700">'
                : '<span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-cream-200 text-cream-500 dark:bg-cream-800 dark:text-cream-400"><i data-lucide="image" class="w-4 h-4"></i></span>';

            $listaBadge = $r->mercado_id
                ? '<span class="inline-flex items-center font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200 text-[10px] px-1.5 py-0.5" title="Vino de Lista Mercado">Lista</span>'
                : '';

            $producto = '<div class="inline-flex items-center gap-2">' . $thumb . '<span class="font-medium">' . e($r->producto?->nombre ?? '—') . '</span>' . $listaBadge . '</div>';

            $tipo = $r->producto?->tipo
                ? '<span class="inline-flex items-center font-semibold rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200 text-[10px] px-1.5 py-0.5">' . e($r->producto->tipo->nombre) . '</span>'
                : '—';

            $cantidad = e($r->cantidad_formateada . ' ' . ($r->producto?->unidad_empaque ?? ''));

            $unitario = $r->cantidad > 0 ? intval($r->valor / $r->cantidad) : 0;
            $unitarioFmt = '$ ' . number_format($unitario, 0, ',', '.');

            $editUrl = route('mercado-dashboard.edit', $r);
            $deleteUrl = route('mercado-dashboard.destroy', $r);
            $csrf = csrf_token();

            $observacion = $r->observacion
                ? '<span class="inline-flex items-center gap-1 align-middle" style="max-width:240px" title="' . e($r->observacion) . '">'
                    . '<i data-lucide="message-circle" class="w-3.5 h-3.5 shrink-0 text-primary-600 dark:text-primary-400"></i>'
                    . '<span class="truncate text-cream-700 dark:text-cream-300" style="min-width:0">' . e($r->observacion) . '</span>'
                  . '</span>'
                : '<span class="text-cream-400 dark:text-cream-600">—</span>';

            $acciones = '<div class="inline-flex items-center gap-2">'
                . '<a href="' . $editUrl . '" class="inline-flex items-center gap-1 text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-100 font-medium"><i data-lucide="edit" class="w-3.5 h-3.5"></i>Editar</a>'
                . '<form action="' . $deleteUrl . '" method="POST" class="inline" onsubmit="return confirm(\'¿Eliminar este registro?\');">'
                . '<input type="hidden" name="_token" value="' . $csrf . '">'
                . '<input type="hidden" name="_method" value="DELETE">'
                . '<button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 font-medium"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Eliminar</button>'
                . '</form>'
                . '</div>';

            return [
                'fecha'     => $r->created_at->format('d M, H:i'),
                'producto'  => $producto,
                'tipo'      => $tipo,
                'cantidad'  => $cantidad,
                'unitario'  => $unitarioFmt,
                'total'     => $r->valor_formateado,
                'observacion' => $observacion,
                'acciones'  => $acciones,
            ];
        })->values()->all();

        $columns = [
            ['key' => 'fecha',    'label' => 'Fecha',     'sortable' => true],
            ['key' => 'producto', 'label' => 'Producto',  'sortable' => false],
            ['key' => 'tipo',     'label' => 'Tipo',      'sortable' => false],
            ['key' => 'cantidad', 'label' => 'Cantidad',  'sortable' => false],
            ['key' => 'unitario', 'label' => 'Unitario',  'sortable' => false],
            ['key' => 'total',    'label' => 'Total',     'sortable' => false],
            ['key' => 'observacion', 'label' => 'Observación', 'sortable' => false],
            ['key' => 'acciones', 'label' => 'Acciones',  'sortable' => false],
        ];

        return view('mercado-dashboard.index', compact(
            'rows', 'columns', 'desde', 'hasta',
            'totalGastado', 'totalCantidad', 'productosDistintos', 'promedioPorRegistro',
            'totalesPorMetodo'
        ));
    }

    public function edit(RegistroMercado $registro): View
    {
        $registro->load('producto.tipo');

        return view('mercado-dashboard.edit', compact('registro'));
    }

    public function update(UpdateRegistroMercadoRequest $request, RegistroMercado $registro): RedirectResponse
    {
        $registro->update($request->validated());

        return redirect()
            ->route('mercado-dashboard.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(RegistroMercado $registro): RedirectResponse
    {
        $registro->delete();

        return redirect()
            ->route('mercado-dashboard.index')
            ->with('success', 'Registro eliminado.');
    }

    public function graficas(Request $request): View
    {
        $periodo = (int) $request->input('periodo', 30);
        $periodo = in_array($periodo, [7, 14, 30, 60, 90]) ? $periodo : 30;

        $desde = today()->subDays($periodo - 1);
        $hasta = today();

        // Gráfica 1: Gasto diario
        $rangoDiario = RegistroMercado::enRangoFechas($desde, $hasta)
            ->selectRaw('DATE(created_at) as dia, SUM(valor) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->mapWithKeys(fn ($v, $k) => [Carbon::parse($k)->toDateString() => (int) $v]);

        $diariasCategorias = [];
        $diariasData = [];
        for ($d = 0; $d < $periodo; $d++) {
            $fecha = $desde->copy()->addDays($d)->toDateString();
            $diariasCategorias[] = Carbon::parse($fecha)->format('d M');
            $diariasData[] = (int) ($rangoDiario[$fecha] ?? 0);
        }

        // Gráfica 2: Gasto por tipo
        $porTipo = DB::table('registros_mercado')
            ->join('productos_mercado', 'registros_mercado.producto_mercado_id', '=', 'productos_mercado.id')
            ->join('tipos_producto_mercado', 'productos_mercado.tipo_id', '=', 'tipos_producto_mercado.id')
            ->whereBetween('registros_mercado.created_at', [
                Carbon::parse($desde)->startOfDay(),
                Carbon::parse($hasta)->endOfDay(),
            ])
            ->selectRaw('tipos_producto_mercado.nombre as tipo, SUM(registros_mercado.valor) as total')
            ->groupBy('tipos_producto_mercado.nombre')
            ->orderByDesc('total')
            ->get();

        $porTipoLabels = $porTipo->pluck('tipo')->all();
        $porTipoSeries = $porTipo->pluck('total')->map(fn ($v) => (int) $v)->all();

        // Gráfica 3: Top 10 productos por valor
        $topProductos = RegistroMercado::with('producto')
            ->enRangoFechas($desde, $hasta)
            ->selectRaw('producto_mercado_id, SUM(valor) as total')
            ->groupBy('producto_mercado_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topLabels = $topProductos->map(fn ($r) => $r->producto?->nombre ?? '—')->all();
        $topData = $topProductos->pluck('total')->map(fn ($v) => (int) $v)->all();

        // Gráfica 4: Variación de unitario por producto
        $productos = ProductoMercado::activos()->orderBy('nombre')->get();

        $productoId = (int) $request->input('producto', 0);
        if (! $productoId) {
            // Producto con más registros en el período
            $masFrecuente = RegistroMercado::enRangoFechas($desde, $hasta)
                ->selectRaw('producto_mercado_id, COUNT(*) as cnt')
                ->groupBy('producto_mercado_id')
                ->orderByDesc('cnt')
                ->first();
            $productoId = $masFrecuente?->producto_mercado_id ?? ($productos->first()?->id ?? 0);
        }

        $variacionRegistros = $productoId
            ? RegistroMercado::where('producto_mercado_id', $productoId)
                ->enRangoFechas($desde, $hasta)
                ->orderBy('created_at')
                ->get()
                ->map(fn ($r) => [
                    'fecha' => $r->created_at->format('d M'),
                    'unitario' => $r->cantidad > 0 ? (int) round($r->valor / $r->cantidad) : 0,
                ])
            : collect();

        $variacionCategorias = $variacionRegistros->pluck('fecha')->all();
        $variacionData = $variacionRegistros->pluck('unitario')->all();

        return view('mercado-dashboard.graficas', compact(
            'periodo', 'productos', 'productoId',
            'diariasCategorias', 'diariasData',
            'porTipoLabels', 'porTipoSeries',
            'topLabels', 'topData',
            'variacionCategorias', 'variacionData'
        ));
    }
}
