<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Obra;
use App\Models\Cliente;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IngresoController extends Controller
{
    /**
     * Exportar el listado de ingresos a Excel (respetando filtros).
     */
    public function exportExcel(Request $request)
    {
        $query = Ingreso::with(['obra', 'cliente']);
        if ($request->filled('obra_id')) $query->where('obra_id', $request->obra_id);
        if ($request->filled('cliente_id')) $query->where('cliente_id', $request->cliente_id);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('fecha_desde')) $query->whereDate('fecha', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('fecha', '<=', $request->fecha_hasta);
        if ($request->filled('importe_min')) $query->where('importe', '>=', (float) $request->importe_min);
        if ($request->filled('importe_max')) $query->where('importe', '<=', (float) $request->importe_max);
        $items = $query->orderByDesc('fecha')->orderByDesc('id')->get();

        $rows = $items->map(fn($i) => [
            optional($i->fecha)->format('d/m/Y'),
            $i->concepto,
            $i->obra?->codigo ?? '-',
            $i->cliente?->nombre_comercial ?? '-',
            (float) $i->importe,
            (float) $i->iva_importe,
            (float) $i->importe_total,
            ucfirst($i->estado),
        ])->toArray();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ListadoExport(['Fecha', 'Concepto', 'Obra', 'Cliente', 'Base imponible', 'IVA', 'Total', 'Estado'], $rows),
            'ingresos_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    /**
     * Mostrar listado de ingresos con filtros y estadísticas.
     */
    public function index(Request $request): View
    {
        $query = Ingreso::with(['obra', 'cliente']);

        // Filtros
        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
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

        // Ordenar y paginar
        $ingresos = $query->orderByDesc('fecha')->orderByDesc('id')->paginate(25)->withQueryString();

        // Estadísticas (cifras en BASE IMPONIBLE, sin IVA, para valoración del rendimiento)
        $stats = [
            'total' => Ingreso::sum('importe'),
            'pendiente' => Ingreso::pendientes()->sum('importe'),
            'cobrado' => Ingreso::cobrados()->sum('importe'),
            'este_mes' => Ingreso::whereMonth('fecha', now()->month)
                                 ->whereYear('fecha', now()->year)
                                 ->sum('importe'),
            // Desglose de IVA y total con impuestos (separación contable)
            'total_iva' => Ingreso::sum('iva_importe'),
            'total_con_iva' => Ingreso::sum('importe_total'),
        ];

        // Datos para filtros
        $obras = Obra::orderBy('codigo')->get(['id', 'codigo', 'nombre']);
        $clientes = Cliente::orderBy('nombre_comercial')->get(['id', 'nombre_comercial']);

        return view('ingresos.index', compact('ingresos', 'stats', 'obras', 'clientes'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create(): View
    {
        $obras = Obra::where('estado', '!=', 'cancelada')
                     ->with('cliente')
                     ->orderBy('codigo')
                     ->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();

        return view('ingresos.create', compact('obras', 'clientes'));
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
     * Guardar nuevo ingreso.
     */
    public function store(Request $request)
    {
        // Aviso de posible duplicado (misma fecha + misma base imponible total), salvo confirmación expresa
        $baseDup = round(collect($request->input('iva_base', []))->sum(fn($v) => floatval($v)), 2);
        if (!$request->boolean('confirmar_duplicado') && $request->filled('fecha') && $baseDup != 0) {
            $dup = Ingreso::whereDate('fecha', $request->fecha)
                ->where('importe', $baseDup)
                ->first();
            if ($dup) {
                return back()->withInput()->with('dup_warning',
                    'Ya existe un ingreso con la misma fecha (' . \Carbon\Carbon::parse($request->fecha)->format('d/m/Y')
                    . ') e importe (' . number_format($baseDup, 2, ',', '.') . ' €): "' . $dup->concepto
                    . '". Si no es un duplicado, marca la casilla y guarda de nuevo.');
            }
        }

        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'cliente_id' => 'required|exists:clientes,id',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'iva_base' => 'required|array|min:1',
            'iva_base.*' => 'required|numeric',
            'iva_pct' => 'required|array|min:1',
            'iva_pct.*' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_prevista_cobro' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
        ], [
            'obra_id.required' => 'La obra es obligatoria.',
            'cliente_id.required' => 'El cliente es obligatorio.',
            'concepto.required' => 'El concepto es obligatorio.',
            'iva_base.required' => 'Indica al menos una base imponible.',
            'iva_base.*.required' => 'La base imponible es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes con desglose de varios IVA + retención
            [$importe, $ivaImporte, $desglose] = $this->calcularDesgloseIva($validated['iva_base'], $validated['iva_pct']);
            $ivaPorcentaje = count($desglose) === 1 ? $desglose[0]['porcentaje'] : ($importe != 0 ? round($ivaImporte / $importe * 100, 2) : 0);
            $retencionPorcentaje = floatval($validated['retencion_porcentaje'] ?? 0);
            $retencionImporte = round($importe * $retencionPorcentaje / 100, 2);
            $importeTotal = round($importe + $ivaImporte - $retencionImporte, 2);

            // Preparar datos
            $data = [
                'obra_id' => $validated['obra_id'],
                'cliente_id' => $validated['cliente_id'],
                'concepto' => $validated['concepto'],
                'descripcion' => $validated['descripcion'] ?? null,
                'importe' => $importe,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_importe' => $ivaImporte,
                'desglose_iva' => $desglose,
                'retencion_porcentaje' => $retencionPorcentaje,
                'retencion_importe' => $retencionImporte,
                'importe_total' => $importeTotal,
                'fecha' => $validated['fecha'],
                'fecha_prevista_cobro' => $validated['fecha_prevista_cobro'] ?? null,
                'forma_pago' => $validated['forma_pago'] ?? null,
                'notas' => $validated['notas'] ?? null,
                'estado' => 'pendiente',
            ];

            $ingreso = Ingreso::create($data);

            // Registrar en auditoría
            Auditoria::registrar('crear', 'ingresos', $ingreso->id, null, $ingreso->toArray());

            DB::commit();

            return redirect()->route('ingresos.show', $ingreso)
                ->with('success', 'Ingreso registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al registrar el ingreso: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de un ingreso.
     */
    public function show(Ingreso $ingreso): View
    {
        $ingreso->load(['obra', 'cliente', 'factura']);

        return view('ingresos.show', compact('ingreso'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Ingreso $ingreso): View
    {
        $obras = Obra::where('estado', '!=', 'cancelada')
                     ->with('cliente')
                     ->orderBy('codigo')
                     ->get();
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();

        return view('ingresos.edit', compact('ingreso', 'obras', 'clientes'));
    }

    /**
     * Actualizar ingreso existente.
     */
    public function update(Request $request, Ingreso $ingreso)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'cliente_id' => 'required|exists:clientes,id',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'iva_base' => 'required|array|min:1',
            'iva_base.*' => 'required|numeric',
            'iva_pct' => 'required|array|min:1',
            'iva_pct.*' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_prevista_cobro' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
        ], [
            'obra_id.required' => 'La obra es obligatoria.',
            'cliente_id.required' => 'El cliente es obligatorio.',
            'concepto.required' => 'El concepto es obligatorio.',
            'iva_base.required' => 'Indica al menos una base imponible.',
            'iva_base.*.required' => 'La base imponible es obligatoria.',
            'fecha.required' => 'La fecha es obligatoria.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes con desglose de varios IVA + retención
            [$importe, $ivaImporte, $desglose] = $this->calcularDesgloseIva($validated['iva_base'], $validated['iva_pct']);
            $ivaPorcentaje = count($desglose) === 1 ? $desglose[0]['porcentaje'] : ($importe != 0 ? round($ivaImporte / $importe * 100, 2) : 0);
            $retencionPorcentaje = floatval($validated['retencion_porcentaje'] ?? 0);
            $retencionImporte = round($importe * $retencionPorcentaje / 100, 2);
            $importeTotal = round($importe + $ivaImporte - $retencionImporte, 2);

            // Preparar datos
            $data = [
                'obra_id' => $validated['obra_id'],
                'cliente_id' => $validated['cliente_id'],
                'concepto' => $validated['concepto'],
                'descripcion' => $validated['descripcion'] ?? null,
                'importe' => $importe,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_importe' => $ivaImporte,
                'desglose_iva' => $desglose,
                'retencion_porcentaje' => $retencionPorcentaje,
                'retencion_importe' => $retencionImporte,
                'importe_total' => $importeTotal,
                'fecha' => $validated['fecha'],
                'fecha_prevista_cobro' => $validated['fecha_prevista_cobro'] ?? null,
                'forma_pago' => $validated['forma_pago'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ];

            // Guardar datos anteriores para auditoría
            $datosAnteriores = $ingreso->toArray();

            $ingreso->update($data);

            // Registrar en auditoría
            Auditoria::registrar('editar', 'ingresos', $ingreso->id, $datosAnteriores, $ingreso->fresh()->toArray());

            DB::commit();

            return redirect()->route('ingresos.show', $ingreso)
                ->with('success', 'Ingreso actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el ingreso: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar ingreso (AJAX).
     */
    public function destroy(Ingreso $ingreso): JsonResponse
    {
        try {
            // Registrar en auditoría antes de eliminar
            Auditoria::registrar('eliminar', 'ingresos', $ingreso->id, $ingreso->toArray(), null);

            $ingreso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ingreso eliminado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el ingreso: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar ingreso como cobrado (AJAX).
     */
    public function marcarCobrado(Request $request, Ingreso $ingreso): JsonResponse
    {
        try {
            $ingreso->update([
                'estado' => 'cobrado',
                // Por defecto: fecha prevista de cobro (vencimiento); si no, la de emisión; si no, hoy
                'fecha_cobro' => $request->fecha_cobro
                    ?? optional($ingreso->fecha_prevista_cobro)->toDateString()
                    ?? optional($ingreso->fecha)->toDateString()
                    ?? now()->toDateString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ingreso marcado como cobrado.',
                'ingreso' => $ingreso->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar ingreso como pendiente (AJAX).
     */
    public function marcarPendiente(Ingreso $ingreso): JsonResponse
    {
        try {
            $ingreso->update([
                'estado' => 'pendiente',
                'fecha_cobro' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ingreso marcado como pendiente.',
                'ingreso' => $ingreso->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener cliente de una obra (AJAX).
     */
    public function getClienteObra(Obra $obra): JsonResponse
    {
        return response()->json([
            'cliente_id' => $obra->cliente_id,
            'cliente_nombre' => $obra->cliente?->nombre_comercial,
            'retencion_porcentaje' => $obra->cliente?->retencion_porcentaje ?? 0,
        ]);
    }
}
