<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaLinea;
use App\Models\Obra;
use App\Models\Cliente;
use App\Models\Auditoria;
use App\Models\EmailLog;
use App\Mail\FacturaEnviadaMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    /**
     * Mostrar listado de facturas con filtros y estadísticas.
     */
    public function index(Request $request): View
    {
        $query = Factura::with(['cliente', 'obra']);

        // Filtros
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('obra_id')) {
            $query->where('obra_id', $request->obra_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('serie')) {
            $query->where('serie', $request->serie);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($q2) use ($search) {
                      $q2->where('nombre_comercial', 'like', "%{$search}%");
                  });
            });
        }

        // Ordenar y paginar
        $facturas = $query->orderByDesc('fecha_emision')->orderByDesc('id')->paginate(25)->withQueryString();

        // Estadísticas
        $stats = [
            'total' => Factura::sum('total'),
            'pendiente' => Factura::pendientes()->sum('total'),
            'cobrado' => Factura::cobradas()->sum('total'),
            'este_mes' => Factura::whereMonth('fecha_emision', now()->month)
                                 ->whereYear('fecha_emision', now()->year)
                                 ->sum('total'),
        ];

        // Datos para filtros
        $clientes = Cliente::orderBy('nombre_comercial')->get(['id', 'nombre_comercial']);
        $obras = Obra::orderBy('codigo')->get(['id', 'codigo', 'nombre']);
        $series = Factura::select('serie')->distinct()->pluck('serie');

        return view('facturas.index', compact('facturas', 'stats', 'clientes', 'obras', 'series'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create(): View
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $obras = Obra::where('estado', '!=', 'cancelada')
                     ->with('cliente')
                     ->orderBy('codigo')
                     ->get();

        return view('facturas.create', compact('clientes', 'obras'));
    }

    /**
     * Guardar nueva factura con líneas.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'obra_id' => 'nullable|exists:obras,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string',
            'lineas' => 'required|array|min:1',
            'lineas.*.concepto' => 'required|string|max:255',
            'lineas.*.descripcion' => 'nullable|string',
            'lineas.*.cantidad' => 'required|numeric|min:0.01',
            'lineas.*.precio_unitario' => 'required|numeric|min:0',
            'lineas.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
        ], [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'lineas.required' => 'Debe añadir al menos una línea a la factura.',
            'lineas.min' => 'Debe añadir al menos una línea a la factura.',
            'lineas.*.concepto.required' => 'El concepto de cada línea es obligatorio.',
            'lineas.*.cantidad.required' => 'La cantidad es obligatoria.',
            'lineas.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'lineas.*.precio_unitario.required' => 'El precio unitario es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            // Crear factura sin número (se genera al emitir)
            $factura = Factura::create([
                'serie' => 'F',
                'numero' => null,
                'cliente_id' => $validated['cliente_id'],
                'obra_id' => $validated['obra_id'] ?? null,
                'fecha_emision' => $validated['fecha_emision'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                'iva_porcentaje' => floatval($validated['iva_porcentaje']),
                'retencion_porcentaje' => floatval($validated['retencion_porcentaje'] ?? 0),
                'notas' => $validated['notas'] ?? null,
                'estado' => 'borrador',
                'base_imponible' => 0,
                'iva_importe' => 0,
                'retencion_importe' => 0,
                'total' => 0,
            ]);

            // Crear líneas
            $orden = 0;
            foreach ($validated['lineas'] as $lineaData) {
                $cantidad = floatval($lineaData['cantidad']);
                $precioUnitario = floatval($lineaData['precio_unitario']);
                $descuentoPct = floatval($lineaData['descuento_porcentaje'] ?? 0);
                $importe = ($cantidad * $precioUnitario) * (1 - $descuentoPct / 100);

                FacturaLinea::create([
                    'factura_id' => $factura->id,
                    'concepto' => $lineaData['concepto'],
                    'descripcion' => $lineaData['descripcion'] ?? null,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_porcentaje' => $descuentoPct,
                    'importe' => $importe,
                    'orden' => $orden++,
                ]);
            }

            // Recalcular totales
            $factura->refresh();
            $factura->calcularTotales();
            $factura->save();

            // Registrar en auditoría
            Auditoria::registrar('crear', 'facturas', $factura->id, null, $factura->toArray());

            DB::commit();

            return redirect()->route('facturas.show', $factura)
                ->with('success', 'Factura creada correctamente como borrador.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al crear la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de una factura.
     */
    public function show(Factura $factura): View
    {
        $factura->load(['cliente', 'obra', 'lineas']);

        return view('facturas.show', compact('factura'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Factura $factura): View
    {
        // Solo se pueden editar facturas en borrador
        if ($factura->estado !== 'borrador') {
            return redirect()->route('facturas.show', $factura)
                ->with('error', 'Solo se pueden editar facturas en estado borrador.');
        }

        $factura->load(['cliente', 'obra', 'lineas']);
        $clientes = Cliente::where('activo', true)->orderBy('nombre_comercial')->get();
        $obras = Obra::where('estado', '!=', 'cancelada')
                     ->with('cliente')
                     ->orderBy('codigo')
                     ->get();

        return view('facturas.edit', compact('factura', 'clientes', 'obras'));
    }

    /**
     * Actualizar factura existente.
     */
    public function update(Request $request, Factura $factura)
    {
        // Solo se pueden actualizar facturas en borrador
        if ($factura->estado !== 'borrador') {
            return redirect()->route('facturas.show', $factura)
                ->with('error', 'Solo se pueden editar facturas en estado borrador.');
        }

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'obra_id' => 'nullable|exists:obras,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string',
            'lineas' => 'required|array|min:1',
            'lineas.*.concepto' => 'required|string|max:255',
            'lineas.*.descripcion' => 'nullable|string',
            'lineas.*.cantidad' => 'required|numeric|min:0.01',
            'lineas.*.precio_unitario' => 'required|numeric|min:0',
            'lineas.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
        ], [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'lineas.required' => 'Debe añadir al menos una línea a la factura.',
            'lineas.min' => 'Debe añadir al menos una línea a la factura.',
        ]);

        DB::beginTransaction();
        try {
            // Guardar datos anteriores para auditoría
            $datosAnteriores = $factura->toArray();

            // Actualizar datos de factura
            $factura->update([
                'cliente_id' => $validated['cliente_id'],
                'obra_id' => $validated['obra_id'] ?? null,
                'fecha_emision' => $validated['fecha_emision'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                'iva_porcentaje' => floatval($validated['iva_porcentaje']),
                'retencion_porcentaje' => floatval($validated['retencion_porcentaje'] ?? 0),
                'notas' => $validated['notas'] ?? null,
            ]);

            // Eliminar líneas existentes y recrear
            $factura->lineas()->delete();

            $orden = 0;
            foreach ($validated['lineas'] as $lineaData) {
                $cantidad = floatval($lineaData['cantidad']);
                $precioUnitario = floatval($lineaData['precio_unitario']);
                $descuentoPct = floatval($lineaData['descuento_porcentaje'] ?? 0);
                $importe = ($cantidad * $precioUnitario) * (1 - $descuentoPct / 100);

                FacturaLinea::create([
                    'factura_id' => $factura->id,
                    'concepto' => $lineaData['concepto'],
                    'descripcion' => $lineaData['descripcion'] ?? null,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento_porcentaje' => $descuentoPct,
                    'importe' => $importe,
                    'orden' => $orden++,
                ]);
            }

            // Recalcular totales
            $factura->refresh();
            $factura->calcularTotales();
            $factura->save();

            // Registrar en auditoría (ya guardamos datos anteriores arriba)
            Auditoria::registrar('editar', 'facturas', $factura->id, $datosAnteriores, $factura->fresh()->toArray());

            DB::commit();

            return redirect()->route('facturas.show', $factura)
                ->with('success', 'Factura actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Error al actualizar la factura: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar factura.
     */
    public function destroy(Factura $factura): JsonResponse
    {
        try {
            // Registrar en auditoría antes de eliminar
            Auditoria::registrar('eliminar', 'facturas', $factura->id, $factura->toArray(), null);

            // Solo borradores o admin puede eliminar
            if ($factura->estado !== 'borrador' && !auth()->user()->hasRole('Administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden eliminar facturas en borrador.',
                ], 403);
            }

            // Eliminar PDF si existe
            if ($factura->pdf_path && file_exists(public_path($factura->pdf_path))) {
                unlink(public_path($factura->pdf_path));
            }

            $factura->lineas()->delete();
            $factura->delete();

            return response()->json([
                'success' => true,
                'message' => 'Factura eliminada correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la factura: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Emitir factura (genera número y cambia estado).
     */
    public function emitir(Factura $factura): JsonResponse
    {
        try {
            if ($factura->estado !== 'borrador') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden emitir facturas en borrador.',
                ], 400);
            }

            // Generar número de factura
            $numero = $this->generarNumeroFactura($factura->serie);

            $factura->update([
                'numero' => $numero,
                'estado' => 'emitida',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura emitida correctamente con número ' . $numero,
                'factura' => $factura->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al emitir la factura: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enviar factura por email al cliente.
     */
    public function enviar(Factura $factura): JsonResponse
    {
        try {
            if ($factura->estado !== 'emitida') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden enviar facturas emitidas.',
                ], 400);
            }

            // Cargar relaciones necesarias
            $factura->load(['cliente', 'obra', 'lineas']);

            // Verificar email del cliente
            $emailCliente = $factura->cliente->email_contacto ?? $factura->cliente->email;
            if (!$emailCliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene email configurado. Configure un email en la ficha del cliente.',
                ], 400);
            }

            // Generar PDF si no existe
            if (!$factura->pdf_path || !file_exists(public_path($factura->pdf_path))) {
                $this->generarPdfInterno($factura);
                $factura->refresh();
            }

            // Enviar email con PDF adjunto
            $asunto = "Factura {$factura->numero} - Manzer Agroforestal";

            try {
                Mail::to($emailCliente)->send(new FacturaEnviadaMail($factura));

                // Actualizar estado de la factura
                $factura->update([
                    'estado' => 'enviada',
                    'email_enviado' => true,
                    'email_enviado_at' => now(),
                ]);

                // Registrar en log de emails
                EmailLog::logEnviado(
                    EmailLog::TIPO_FACTURA,
                    $emailCliente,
                    $asunto,
                    $factura
                );

                return response()->json([
                    'success' => true,
                    'message' => "Factura enviada correctamente a {$emailCliente}",
                    'factura' => $factura->fresh(),
                ]);

            } catch (\Exception $mailError) {
                // Registrar error en log
                EmailLog::logFallido(
                    EmailLog::TIPO_FACTURA,
                    $emailCliente,
                    $asunto,
                    $mailError->getMessage(),
                    $factura
                );

                Log::error("Error enviando factura por email", [
                    'factura_id' => $factura->id,
                    'email' => $emailCliente,
                    'error' => $mailError->getMessage()
                ]);

                // Marcar como enviada pero informar del error de email
                $factura->update(['estado' => 'enviada']);

                return response()->json([
                    'success' => true,
                    'message' => 'Factura marcada como enviada, pero el email no pudo ser enviado: ' . $mailError->getMessage(),
                    'email_error' => true,
                    'factura' => $factura->fresh(),
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar PDF interno sin devolver respuesta HTTP.
     */
    private function generarPdfInterno(Factura $factura): void
    {
        $factura->load(['cliente', 'obra', 'lineas']);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        $año = $factura->fecha_emision->format('Y');
        $mes = $factura->fecha_emision->format('m');
        $directorio = public_path("uploads/facturas/{$año}/{$mes}");

        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombreArchivo = 'factura_' . ($factura->numero ?? 'borrador_' . $factura->id) . '_' . time() . '.pdf';
        $rutaCompleta = "{$directorio}/{$nombreArchivo}";

        $pdf->save($rutaCompleta);

        $factura->update([
            'pdf_path' => "uploads/facturas/{$año}/{$mes}/{$nombreArchivo}"
        ]);
    }

    /**
     * Marcar factura como cobrada.
     */
    public function cobrar(Request $request, Factura $factura): JsonResponse
    {
        try {
            if (!in_array($factura->estado, ['emitida', 'enviada'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden cobrar facturas emitidas o enviadas.',
                ], 400);
            }

            $factura->update([
                'estado' => 'cobrada',
                'fecha_cobro' => $request->fecha_cobro ?? now()->toDateString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura marcada como cobrada.',
                'factura' => $factura->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Anular factura.
     */
    public function anular(Factura $factura): JsonResponse
    {
        try {
            if (in_array($factura->estado, ['cobrada', 'anulada'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede anular una factura cobrada o ya anulada.',
                ], 400);
            }

            $factura->update(['estado' => 'anulada']);

            return response()->json([
                'success' => true,
                'message' => 'Factura anulada correctamente.',
                'factura' => $factura->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generar y mostrar PDF de la factura.
     */
    public function generarPdf(Factura $factura)
    {
        $factura->load(['cliente', 'obra', 'lineas']);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        // Guardar PDF en servidor
        $año = $factura->fecha_emision->format('Y');
        $mes = $factura->fecha_emision->format('m');
        $directorio = public_path("uploads/facturas/{$año}/{$mes}");

        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $nombreArchivo = 'factura_' . ($factura->numero ?? 'borrador_' . $factura->id) . '_' . time() . '.pdf';
        $rutaCompleta = "{$directorio}/{$nombreArchivo}";

        $pdf->save($rutaCompleta);

        // Actualizar path en BD
        $factura->update([
            'pdf_path' => "uploads/facturas/{$año}/{$mes}/{$nombreArchivo}"
        ]);

        return $pdf->stream('factura_' . ($factura->numero ?? $factura->id) . '.pdf');
    }

    /**
     * Descargar PDF existente.
     */
    public function descargarPdf(Factura $factura)
    {
        if (!$factura->pdf_path || !file_exists(public_path($factura->pdf_path))) {
            return redirect()->route('facturas.pdf', $factura);
        }

        return response()->download(
            public_path($factura->pdf_path),
            'factura_' . ($factura->numero ?? $factura->id) . '.pdf'
        );
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

    /**
     * Generar número de factura automático.
     */
    private function generarNumeroFactura(string $serie = 'F'): string
    {
        $año = date('Y');

        $ultimoNumero = Factura::where('serie', $serie)
            ->whereYear('fecha_emision', $año)
            ->whereNotNull('numero')
            ->orderByRaw("CAST(SUBSTRING_INDEX(numero, '-', -1) AS UNSIGNED) DESC")
            ->value('numero');

        if ($ultimoNumero) {
            preg_match('/(\d+)$/', $ultimoNumero, $matches);
            $siguiente = intval($matches[1]) + 1;
        } else {
            $siguiente = 1;
        }

        return sprintf('%s-%d-%05d', $serie, $año, $siguiente);
    }
}
