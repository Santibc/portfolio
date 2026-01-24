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
        if ($request->filled('proveedor')) {
            $query->where('proveedor', 'like', '%' . $request->proveedor . '%');
        }

        // Ordenar y paginar
        $gastos = $query->orderByDesc('fecha')->orderByDesc('id')->paginate(25)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => Gasto::sum('importe_total'),
            'pendiente' => Gasto::pendientes()->sum('importe_total'),
            'pagado' => Gasto::pagados()->sum('importe_total'),
            'este_mes' => Gasto::whereMonth('fecha', now()->month)
                              ->whereYear('fecha', now()->year)
                              ->sum('importe_total'),
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
     * Guardar nuevo gasto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gasto_categoria_id' => 'required|exists:gasto_categorias,id',
            'obra_id' => 'nullable|exists:obras,id',
            'proveedor' => 'nullable|string|max:255',
            'concepto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'importe' => 'required|numeric|min:0.01',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notas' => 'nullable|string',
        ], [
            'gasto_categoria_id.required' => 'La categoría es obligatoria.',
            'concepto.required' => 'El concepto es obligatorio.',
            'importe.required' => 'El importe es obligatorio.',
            'importe.min' => 'El importe debe ser mayor a 0.',
            'fecha.required' => 'La fecha es obligatoria.',
            'documento.mimes' => 'El documento debe ser PDF, JPG o PNG.',
            'documento.max' => 'El documento no puede superar 5MB.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes
            $importe = floatval($validated['importe']);
            $ivaPorcentaje = floatval($validated['iva_porcentaje']);
            $ivaImporte = $importe * ($ivaPorcentaje / 100);
            $importeTotal = $importe + $ivaImporte;

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
            'importe' => 'required|numeric|min:0.01',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'forma_pago' => 'nullable|string|max:100',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notas' => 'nullable|string',
        ], [
            'gasto_categoria_id.required' => 'La categoría es obligatoria.',
            'concepto.required' => 'El concepto es obligatorio.',
            'importe.required' => 'El importe es obligatorio.',
            'importe.min' => 'El importe debe ser mayor a 0.',
            'fecha.required' => 'La fecha es obligatoria.',
            'documento.mimes' => 'El documento debe ser PDF, JPG o PNG.',
            'documento.max' => 'El documento no puede superar 5MB.',
        ]);

        DB::beginTransaction();
        try {
            // Calcular importes
            $importe = floatval($validated['importe']);
            $ivaPorcentaje = floatval($validated['iva_porcentaje']);
            $ivaImporte = $importe * ($ivaPorcentaje / 100);
            $importeTotal = $importe + $ivaImporte;

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
                'fecha_pago' => $request->fecha_pago ?? now()->toDateString(),
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
