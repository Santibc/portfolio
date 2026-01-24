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

        // Ordenar y paginar
        $ingresos = $query->orderByDesc('fecha')->orderByDesc('id')->paginate(25)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => Ingreso::sum('importe_total'),
            'pendiente' => Ingreso::pendientes()->sum('importe_total'),
            'cobrado' => Ingreso::cobrados()->sum('importe_total'),
            'este_mes' => Ingreso::whereMonth('fecha', now()->month)
                                 ->whereYear('fecha', now()->year)
                                 ->sum('importe_total'),
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
     * Guardar nuevo ingreso.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'cliente_id' => 'required|exists:clientes,id',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'importe' => 'required|numeric|min:0.01',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_prevista_cobro' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
        ], [
            'obra_id.required' => 'La obra es obligatoria.',
            'cliente_id.required' => 'El cliente es obligatorio.',
            'concepto.required' => 'El concepto es obligatorio.',
            'importe.required' => 'El importe es obligatorio.',
            'importe.min' => 'El importe debe ser mayor a 0.',
            'fecha.required' => 'La fecha es obligatoria.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes
            $importe = floatval($validated['importe']);
            $ivaPorcentaje = floatval($validated['iva_porcentaje']);
            $retencionPorcentaje = floatval($validated['retencion_porcentaje'] ?? 0);

            $ivaImporte = $importe * ($ivaPorcentaje / 100);
            $retencionImporte = $importe * ($retencionPorcentaje / 100);
            $importeTotal = $importe + $ivaImporte - $retencionImporte;

            // Preparar datos
            $data = [
                'obra_id' => $validated['obra_id'],
                'cliente_id' => $validated['cliente_id'],
                'concepto' => $validated['concepto'],
                'descripcion' => $validated['descripcion'] ?? null,
                'importe' => $importe,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_importe' => $ivaImporte,
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
            'importe' => 'required|numeric|min:0.01',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_prevista_cobro' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
        ], [
            'obra_id.required' => 'La obra es obligatoria.',
            'cliente_id.required' => 'El cliente es obligatorio.',
            'concepto.required' => 'El concepto es obligatorio.',
            'importe.required' => 'El importe es obligatorio.',
            'importe.min' => 'El importe debe ser mayor a 0.',
            'fecha.required' => 'La fecha es obligatoria.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes
            $importe = floatval($validated['importe']);
            $ivaPorcentaje = floatval($validated['iva_porcentaje']);
            $retencionPorcentaje = floatval($validated['retencion_porcentaje'] ?? 0);

            $ivaImporte = $importe * ($ivaPorcentaje / 100);
            $retencionImporte = $importe * ($retencionPorcentaje / 100);
            $importeTotal = $importe + $ivaImporte - $retencionImporte;

            // Preparar datos
            $data = [
                'obra_id' => $validated['obra_id'],
                'cliente_id' => $validated['cliente_id'],
                'concepto' => $validated['concepto'],
                'descripcion' => $validated['descripcion'] ?? null,
                'importe' => $importe,
                'iva_porcentaje' => $ivaPorcentaje,
                'iva_importe' => $ivaImporte,
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
                'fecha_cobro' => $request->fecha_cobro ?? now()->toDateString(),
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
