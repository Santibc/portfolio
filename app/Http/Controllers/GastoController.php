<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\GastoCategoria;
use App\Models\Obra;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GastoController extends Controller
{
    /**
     * Exportar el listado de gastos a Excel (respetando filtros).
     */
    public function exportExcel(Request $request)
    {
        $query = Gasto::with(['categoria', 'obra']);
        if ($request->filled('obra_id')) $query->where('obra_id', $request->obra_id);
        if ($request->filled('gasto_categoria_id')) $query->where('gasto_categoria_id', $request->gasto_categoria_id);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('tipo')) {
            if ($request->tipo === 'directo') $query->directos();
            elseif ($request->tipo === 'indirecto') $query->indirectos();
        }
        if ($request->filled('fecha_desde')) $query->whereDate('fecha', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('fecha', '<=', $request->fecha_hasta);
        if ($request->filled('importe_min')) $query->where('importe', '>=', (float) $request->importe_min);
        if ($request->filled('importe_max')) $query->where('importe', '<=', (float) $request->importe_max);
        if ($request->filled('proveedor')) $query->where('proveedor', 'like', '%' . $request->proveedor . '%');
        $items = $query->orderByDesc('fecha')->orderByDesc('id')->get();

        $rows = $items->map(fn($g) => [
            optional($g->fecha)->format('d/m/Y'),
            $g->concepto,
            $g->proveedor ?? '-',
            $g->categoria?->nombre ?? '-',
            $g->obra?->codigo ?? '-',
            (float) $g->importe,
            (float) $g->iva_importe,
            (float) ($g->irpf_importe ?? 0),
            (float) $g->importe_total,
            ucfirst($g->estado),
        ])->toArray();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ListadoExport(['Fecha', 'Concepto', 'Proveedor', 'Categoría', 'Obra', 'Base imponible', 'IVA', 'IRPF', 'Total', 'Estado'], $rows),
            'gastos_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    /**
     * Mostrar listado de gastos con filtros y estadísticas.
     */
    public function index(Request $request): View
    {
        $query = Gasto::with(['categoria', 'obra']);

        // Filtros
        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }
        if ($request->filled('gasto_categoria_id')) {
            $query->where('gasto_categoria_id', $request->gasto_categoria_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo')) {
            if ($request->tipo === 'directo') {
                $query->directos();
            } elseif ($request->tipo === 'indirecto') {
                $query->indirectos();
            }
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }
        if ($request->filled('importe_min')) {
            $query->where('importe', '>=', (float) $request->importe_min);
        }
        if ($request->filled('importe_max')) {
            $query->where('importe', '<=', (float) $request->importe_max);
        }
        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'like', '%' . $request->proveedor . '%');
        }

        // Ordenar y paginar
        $gastos = $query->orderByDesc('fecha')->orderByDesc('id')->paginate(25)->withQueryString();

        // Estadísticas (cifras en BASE IMPONIBLE, sin IVA, para valoración del rendimiento)
        $stats = [
            'total' => Gasto::sum('importe'),
            'pendiente' => Gasto::pendientes()->sum('importe'),
            'pagado' => Gasto::pagados()->sum('importe'),
            'este_mes' => Gasto::whereMonth('fecha', now()->month)
                              ->whereYear('fecha', now()->year)
                              ->sum('importe'),
            // Desglose de IVA y total con impuestos (separación contable)
            'total_iva' => Gasto::sum('iva_importe'),
            'total_con_iva' => Gasto::sum('importe_total'),
        ];

        // Datos para filtros
        $obras = Obra::orderBy('codigo')->get(['id', 'codigo', 'nombre']);
        $categorias = GastoCategoria::orderBy('nombre')->get(['id', 'nombre', 'tipo']);

        return view('gastos.index', compact('gastos', 'stats', 'obras', 'categorias'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create(): View
    {
        $obras = Obra::where('estado', '!=', 'cancelada')
                     ->orderBy('codigo')
                     ->get(['id', 'codigo', 'nombre']);
        $categorias = GastoCategoria::orderBy('tipo')->orderBy('nombre')->get();

        return view('gastos.create', compact('obras', 'categorias'));
    }

    /**
     * Calcular base total, IVA total y desglose a partir de las líneas de IVA.
     */
    private function calcularDesgloseIva(array $bases, array $pcts): array
    {
        $base = 0; $iva = 0; $desglose = [];
        foreach ($bases as $i => $b) {
            $b = floatval($b);
            $p = floatval($pcts[$i] ?? 0);
            $imp = round($b * $p / 100, 2);
            $base += $b;
            $iva += $imp;
            $desglose[] = ['base' => round($b, 2), 'porcentaje' => $p, 'importe' => $imp];
        }
        return [round($base, 2), round($iva, 2), $desglose];
    }

    /**
     * Guardar nuevo gasto.
     */
    public function store(Request $request)
    {
        // Aviso de posible duplicado (misma fecha + misma base imponible total), salvo confirmación expresa
        $baseDup = round(collect($request->input('iva_base', []))->sum(fn($v) => floatval($v)), 2);
        if (!$request->boolean('confirmar_duplicado') && $request->filled('fecha') && $baseDup != 0) {
            $dup = Gasto::whereDate('fecha', $request->fecha)
                ->where('importe', $baseDup)
                ->first();
            if ($dup) {
                return back()->withInput()->with('dup_warning',
                    'Ya existe un gasto con la misma fecha (' . \Carbon\Carbon::parse($request->fecha)->format('d/m/Y')
                    . ') e importe (' . number_format($baseDup, 2, ',', '.') . ' €): "' . $dup->concepto
                    . '". Si no es un duplicado, marca la casilla y guarda de nuevo.');
            }
        }

        $validated = $request->validate([
            'gasto_categoria_id' => 'required|exists:gasto_categorias,id',
            'obra_id' => 'nullable|exists:obras,id',
            'proveedor' => 'nullable|string|max:255',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'iva_base' => 'required|array|min:1',
            'iva_base.*' => 'required|numeric',
            'iva_pct' => 'required|array|min:1',
            'iva_pct.*' => 'required|numeric|min:0|max:100',
            'irpf_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'documento' => 'nullable|file|max:5120',
            'notas' => 'nullable|string',
        ], [
            'gasto_categoria_id.required' => 'La categoría es obligatoria.',
            'concepto.required' => 'El concepto es obligatorio.',
            'iva_base.required' => 'Indica al menos una base imponible.',
            'iva_base.*.required' => 'La base imponible es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'documento.max' => 'El documento no puede superar 5MB.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes con desglose de varios IVA + IRPF
            [$importe, $ivaImporte, $desglose] = $this->calcularDesgloseIva($validated['iva_base'], $validated['iva_pct']);
            $ivaPorcentaje = count($desglose) === 1 ? $desglose[0]['porcentaje'] : ($importe != 0 ? round($ivaImporte / $importe * 100, 2) : 0);
            $irpfPorcentaje = floatval($validated['irpf_porcentaje'] ?? 0);
            $irpfImporte = round($importe * $irpfPorcentaje / 100, 2);
            $importeTotal = round($importe + $ivaImporte - $irpfImporte, 2);

            // Preparar datos
            $data = [
                'gasto_categoria_id' => $validated['gasto_categoria_id'],
                'obra_id' => $validated['obra_id'] ?? null,
                'proveedor' => $validated['proveedor'] ?? null,
                'concepto' => $validated['concepto'],
                'descripcion' => $validated['descripcion'] ?? null,
                'importe' => $importe,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_importe' => $ivaImporte,
                'irpf_porcentaje' => $irpfPorcentaje,
                'irpf_importe' => $irpfImporte,
                'desglose_iva' => $desglose,
                'importe_total' => $importeTotal,
                'fecha' => $validated['fecha'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                'forma_pago' => $validated['forma_pago'] ?? null,
                'notas' => $validated['notas'] ?? null,
                'estado' => 'pendiente',
            ];

            $gasto = Gasto::create($data);

            // Registrar en auditoría
            Auditoria::registrar('crear', 'gastos', $gasto->id, null, $gasto->toArray());

            // Subir documento si existe
            if ($request->hasFile('documento')) {
                $documento = $request->file('documento');
                $año = date('Y');
                $mes = date('m');
                $directorio = public_path("uploads/gastos/{$año}/{$mes}");

                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $nombreArchivo = "gasto_{$gasto->id}_" . time() . '.' . $documento->getClientOriginalExtension();
                $documento->move($directorio, $nombreArchivo);

                $gasto->update(['documento_path' => "uploads/gastos/{$año}/{$mes}/{$nombreArchivo}"]);
            }

            DB::commit();

            return redirect()->route('gastos.show', $gasto)
                ->with('success', 'Gasto registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al registrar el gasto: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de un gasto.
     */
    public function show(Gasto $gasto): View
    {
        $gasto->load(['categoria', 'obra', 'obra.cliente']);

        return view('gastos.show', compact('gasto'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Gasto $gasto): View
    {
        $obras = Obra::where('estado', '!=', 'cancelada')
                     ->orderBy('codigo')
                     ->get(['id', 'codigo', 'nombre']);
        $categorias = GastoCategoria::orderBy('tipo')->orderBy('nombre')->get();

        return view('gastos.edit', compact('gasto', 'obras', 'categorias'));
    }

    /**
     * Actualizar gasto existente.
     */
    public function update(Request $request, Gasto $gasto)
    {
        $validated = $request->validate([
            'gasto_categoria_id' => 'required|exists:gasto_categorias,id',
            'obra_id' => 'nullable|exists:obras,id',
            'proveedor' => 'nullable|string|max:255',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'iva_base' => 'required|array|min:1',
            'iva_base.*' => 'required|numeric',
            'iva_pct' => 'required|array|min:1',
            'iva_pct.*' => 'required|numeric|min:0|max:100',
            'irpf_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'documento' => 'nullable|file|max:5120',
            'notas' => 'nullable|string',
        ], [
            'gasto_categoria_id.required' => 'La categoría es obligatoria.',
            'concepto.required' => 'El concepto es obligatorio.',
            'iva_base.required' => 'Indica al menos una base imponible.',
            'iva_base.*.required' => 'La base imponible es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
            'documento.max' => 'El documento no puede superar 5MB.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes con desglose de varios IVA + IRPF
            [$importe, $ivaImporte, $desglose] = $this->calcularDesgloseIva($validated['iva_base'], $validated['iva_pct']);
            $ivaPorcentaje = count($desglose) === 1 ? $desglose[0]['porcentaje'] : ($importe != 0 ? round($ivaImporte / $importe * 100, 2) : 0);
            $irpfPorcentaje = floatval($validated['irpf_porcentaje'] ?? 0);
            $irpfImporte = round($importe * $irpfPorcentaje / 100, 2);
            $importeTotal = round($importe + $ivaImporte - $irpfImporte, 2);

            // Preparar datos
            $data = [
                'gasto_categoria_id' => $validated['gasto_categoria_id'],
                'obra_id' => $validated['obra_id'] ?? null,
                'proveedor' => $validated['proveedor'] ?? null,
                'concepto' => $validated['concepto'],
                'descripcion' => $validated['descripcion'] ?? null,
                'importe' => $importe,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_importe' => $ivaImporte,
                'irpf_porcentaje' => $irpfPorcentaje,
                'irpf_importe' => $irpfImporte,
                'desglose_iva' => $desglose,
                'importe_total' => $importeTotal,
                'fecha' => $validated['fecha'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                'forma_pago' => $validated['forma_pago'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ];

            // Subir nuevo documento si existe
            if ($request->hasFile('documento')) {
                // Eliminar documento anterior si existe
                if ($gasto->documento_path && file_exists(public_path($gasto->documento_path))) {
                    unlink(public_path($gasto->documento_path));
                }

                $documento = $request->file('documento');
                $año = date('Y');
                $mes = date('m');
                $directorio = public_path("uploads/gastos/{$año}/{$mes}");

                if (!file_exists($directorio)) {
                    mkdir($directorio, 0755, true);
                }

                $nombreArchivo = "gasto_{$gasto->id}_" . time() . '.' . $documento->getClientOriginalExtension();
                $documento->move($directorio, $nombreArchivo);

                $data['documento_path'] = "uploads/gastos/{$año}/{$mes}/{$nombreArchivo}";
            }

            // Guardar datos anteriores para auditoría
            $datosAnteriores = $gasto->toArray();

            $gasto->update($data);

            // Registrar en auditoría
            Auditoria::registrar('editar', 'gastos', $gasto->id, $datosAnteriores, $gasto->fresh()->toArray());

            DB::commit();

            return redirect()->route('gastos.show', $gasto)
                ->with('success', 'Gasto actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el gasto: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar gasto (AJAX).
     */
    public function destroy(Gasto $gasto): JsonResponse
    {
        try {
            // Registrar en auditoría antes de eliminar
            Auditoria::registrar('eliminar', 'gastos', $gasto->id, $gasto->toArray(), null);

            // Eliminar documento si existe
            if ($gasto->documento_path && file_exists(public_path($gasto->documento_path))) {
                unlink(public_path($gasto->documento_path));
            }

            $gasto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gasto eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el gasto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar gasto como pagado (AJAX).
     */
    public function marcarPagado(Request $request, Gasto $gasto): JsonResponse
    {
        try {
            $gasto->update([
                'estado' => 'pagado',
                // Por defecto: fecha de vencimiento; si no, la de emisión; si no, hoy
                'fecha_pago' => $request->fecha_pago
                    ?? optional($gasto->fecha_vencimiento)->toDateString()
                    ?? optional($gasto->fecha)->toDateString()
                    ?? now()->toDateString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gasto marcado como pagado.',
                'gasto' => $gasto->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar gasto como pendiente (AJAX).
     */
    public function marcarPendiente(Gasto $gasto): JsonResponse
    {
        try {
            $gasto->update([
                'estado' => 'pendiente',
                'fecha_pago' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Gasto marcado como pendiente.',
                'gasto' => $gasto->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
