<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaLinea;
use App\Models\Obra;
use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\Auditoria;
use App\Models\EmailLog;
use App\Mail\FacturaEnviadaMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    /**
     * Exportar el listado de facturas a Excel (respetando filtros).
     */
    public function exportExcel(Request $request)
    {
        $query = Factura::with(['cliente', 'obra']);
        if ($request->filled('cliente_id')) $query->where('cliente_id', $request->cliente_id);
        if ($request->filled('obra_id')) $query->where('obra_id', $request->obra_id);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('serie')) $query->where('serie', $request->serie);
        if ($request->filled('fecha_desde')) $query->whereDate('fecha_emision', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $query->whereDate('fecha_emision', '<=', $request->fecha_hasta);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('numero', 'like', "%{$s}%")->orWhereHas('cliente', fn($q2) => $q2->where('nombre_comercial', 'like', "%{$s}%")));
        }
        $items = $query->orderByDesc('fecha_emision')->orderByDesc('id')->get();

        $rows = $items->map(fn($f) => [
            $f->numero ?? 'Borrador',
            optional($f->fecha_emision)->format('d/m/Y'),
            $f->fecha_vencimiento ? $f->fecha_vencimiento->format('d/m/Y') : '-',
            $f->cliente?->nombre_comercial ?? '-',
            $f->obra?->codigo ?? '-',
            (float) $f->base_imponible,
            (float) $f->iva_importe,
            (float) $f->total,
            ucfirst($f->estado),
        ])->toArray();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ListadoExport(['Número', 'Emisión', 'Vencimiento', 'Cliente', 'Obra', 'Base', 'IVA', 'Total', 'Estado'], $rows),
            'facturas_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

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
            'serie' => 'nullable|string|max:20',
            'numero' => ['nullable', 'string', 'max:50', Rule::unique('facturas', 'numero')],
            'cliente_id' => 'required|exists:clientes,id',
            'obra_id' => 'nullable|exists:obras,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'retencion_porcentaje' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string',
            'footer_text' => 'nullable|string|max:1000',
            'lineas' => 'required|array|min:1',
            'lineas.*.concepto' => 'required|string|max:255',
            'lineas.*.descripcion' => 'nullable|string',
            'lineas.*.cantidad' => 'required|numeric|min:0.01',
            'lineas.*.precio_unitario' => 'required|numeric|min:0',
            'lineas.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'lineas.*.grupo' => 'nullable|string|max:255',
        ], [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'lineas.required' => 'Debe añadir al menos una línea a la factura.',
            'lineas.min' => 'Debe añadir al menos una línea a la factura.',
            'lineas.*.concepto.required' => 'El concepto de cada línea es obligatorio.',
            'lineas.*.cantidad.required' => 'La cantidad es obligatoria.',
            'lineas.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'lineas.*.precio_unitario.required' => 'El precio unitario es obligatorio.',
            'footer_text.max' => 'El texto del pie de página no puede exceder 1000 caracteres.',
        ]);

        DB::beginTransaction();
        try {
            // Crear factura sin número (se genera al emitir)
            $factura = Factura::create([
                'serie' => $validated['serie'] ?? 'F',
                'numero' => $validated['numero'] ?? null,
                'cliente_id' => $validated['cliente_id'],
                'obra_id' => $validated['obra_id'] ?? null,
                'fecha_emision' => $validated['fecha_emision'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                'iva_porcentaje' => floatval($validated['iva_porcentaje']),
                'retencion_porcentaje' => floatval($validated['retencion_porcentaje'] ?? 0),
                'notas' => $validated['notas'] ?? null,
                'footer_text' => $validated['footer_text'] ?? Factura::DEFAULT_FOOTER_TEXT,
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
                    'grupo' => !empty($lineaData['grupo']) ? $lineaData['grupo'] : null,
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
            'footer_text' => 'nullable|string|max:1000',
            'lineas' => 'required|array|min:1',
            'lineas.*.concepto' => 'required|string|max:255',
            'lineas.*.descripcion' => 'nullable|string',
            'lineas.*.cantidad' => 'required|numeric|min:0.01',
            'lineas.*.precio_unitario' => 'required|numeric|min:0',
            'lineas.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'lineas.*.grupo' => 'nullable|string|max:255',
        ], [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'lineas.required' => 'Debe añadir al menos una línea a la factura.',
            'lineas.min' => 'Debe añadir al menos una línea a la factura.',
            'footer_text.max' => 'El texto del pie de página no puede exceder 1000 caracteres.',
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
                'footer_text' => $validated['footer_text'] ?? $factura->footer_text,
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
                    'grupo' => !empty($lineaData['grupo']) ? $lineaData['grupo'] : null,
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

            // Usar el número introducido manualmente si existe; si no, generarlo automáticamente
            $numero = $factura->numero ?: $this->generarNumeroFactura($factura->serie);

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
     * Enviar factura por email a uno o múltiples destinatarios.
     */
    public function enviar(Request $request, Factura $factura): JsonResponse
    {
        try {
            // Validar estado
            if ($factura->estado !== 'emitida') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden enviar facturas emitidas.',
                ], 400);
            }

            // Cargar relaciones necesarias
            $factura->load(['cliente', 'obra', 'lineas']);

            // Validar emails recibidos
            $validated = $request->validate([
                'emails' => 'required|array|min:1|max:10',
                'emails.*' => 'required|email|max:255',
            ], [
                'emails.required' => 'Debe seleccionar al menos un destinatario.',
                'emails.min' => 'Debe seleccionar al menos un destinatario.',
                'emails.max' => 'No puede enviar a más de 10 destinatarios a la vez.',
                'emails.*.email' => 'Uno de los emails proporcionados no es válido.',
            ]);

            $emails = $validated['emails'];

            // Eliminar duplicados (case-insensitive)
            $emails = array_unique(array_map('strtolower', $emails));

            if (empty($emails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se proporcionaron emails válidos.',
                ], 400);
            }

            // Generar PDF si no existe
            if (!$factura->pdf_path || !file_exists(public_path($factura->pdf_path))) {
                $this->generarPdfInterno($factura);
                $factura->refresh();
            }

            // Preparar asunto
            $asunto = "Factura {$factura->numero} - Manzer Agroforestal";

            // Enviar emails
            $resultados = $this->enviarEmailsMultiples($factura, $emails, $asunto);

            // Analizar resultados
            $totalEnviados = count($resultados['exitosos']);
            $totalFallidos = count($resultados['fallidos']);

            // Actualizar estado de factura
            $factura->update([
                'estado' => 'enviada',
                'email_enviado' => $totalEnviados > 0,
                'email_enviado_at' => now(),
            ]);

            // Preparar mensaje de respuesta
            if ($totalFallidos === 0) {
                // Todo exitoso
                return response()->json([
                    'success' => true,
                    'message' => "Factura enviada correctamente a {$totalEnviados} destinatario(s).",
                    'detalles' => [
                        'enviados' => $resultados['exitosos'],
                        'total' => $totalEnviados,
                    ],
                    'factura' => $factura->fresh(),
                ]);
            } elseif ($totalEnviados > 0) {
                // Parcialmente exitoso
                return response()->json([
                    'success' => true,
                    'message' => "Factura enviada a {$totalEnviados} destinatario(s), pero falló en {$totalFallidos}.",
                    'warning' => true,
                    'detalles' => [
                        'enviados' => $resultados['exitosos'],
                        'fallidos' => $resultados['fallidos'],
                    ],
                    'factura' => $factura->fresh(),
                ]);
            } else {
                // Todos fallaron
                return response()->json([
                    'success' => false,
                    'message' => "No se pudo enviar a ningún destinatario. Errores: " . implode(', ', array_column($resultados['fallidos'], 'error')),
                    'detalles' => [
                        'fallidos' => $resultados['fallidos'],
                    ],
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(' ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Error enviando factura", [
                'factura_id' => $factura->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enviar email a múltiples destinatarios con seguimiento individual
     */
    private function enviarEmailsMultiples(Factura $factura, array $emails, string $asunto): array
    {
        $exitosos = [];
        $fallidos = [];

        foreach ($emails as $email) {
            try {
                // Enviar usando Mail::to() - Laravel lo encola automáticamente por ShouldQueue
                Mail::to($email)->send(new FacturaEnviadaMail($factura));

                $exitosos[] = $email;

                // Log individual exitoso
                EmailLog::logEnviado(
                    EmailLog::TIPO_FACTURA,
                    $email,
                    $asunto,
                    $factura
                );

            } catch (\Exception $mailError) {
                $fallidos[] = [
                    'email' => $email,
                    'error' => $mailError->getMessage(),
                ];

                // Log individual fallido
                EmailLog::logFallido(
                    EmailLog::TIPO_FACTURA,
                    $email,
                    $asunto,
                    $mailError->getMessage(),
                    $factura
                );

                Log::warning("Error enviando factura a email específico", [
                    'factura_id' => $factura->id,
                    'email' => $email,
                    'error' => $mailError->getMessage()
                ]);
            }
        }

        return [
            'exitosos' => $exitosos,
            'fallidos' => $fallidos,
        ];
    }

    /**
     * Obtener todos los emails disponibles de un cliente para envío de factura
     */
    public function getClienteEmails(Factura $factura): JsonResponse
    {
        try {
            $factura->load('cliente.emailsAdicionalesActivos');

            if (!$factura->cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'La factura no tiene cliente asociado.',
                ], 404);
            }

            $emails = $factura->cliente->todos_emails;

            if (empty($emails)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene emails configurados.',
                    'emails' => [],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'emails' => $emails,
                'cliente' => [
                    'id' => $factura->cliente->id,
                    'nombre' => $factura->cliente->nombre_comercial,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo emails: ' . $e->getMessage(),
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

            DB::beginTransaction();

            $fechaCobro = $request->fecha_cobro ?? now()->toDateString();

            $factura->update([
                'estado' => 'cobrada',
                'fecha_cobro' => $fechaCobro,
            ]);

            // Generar automáticamente el ingreso correspondiente (si no existe ya)
            $ingresoCreado = false;
            if (!Ingreso::where('factura_id', $factura->id)->exists()) {
                $ingreso = Ingreso::create([
                    'obra_id' => $factura->obra_id,
                    'cliente_id' => $factura->cliente_id,
                    'factura_id' => $factura->id,
                    'concepto' => 'Cobro factura ' . ($factura->numero ?? ('borrador #' . $factura->id)),
                    'descripcion' => 'Ingreso generado automáticamente al cobrar la factura.',
                    'importe' => $factura->base_imponible,
                    'iva_porcentaje' => $factura->iva_porcentaje,
                    'iva_importe' => $factura->iva_importe,
                    'retencion_porcentaje' => $factura->retencion_porcentaje,
                    'retencion_importe' => $factura->retencion_importe,
                    'importe_total' => $factura->total,
                    'fecha' => $factura->fecha_emision,
                    'fecha_prevista_cobro' => $factura->fecha_vencimiento,
                    'fecha_cobro' => $fechaCobro,
                    'estado' => 'cobrado',
                ]);
                Auditoria::registrar('crear', 'ingresos', $ingreso->id, null, $ingreso->toArray());
                $ingresoCreado = true;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $ingresoCreado
                    ? 'Factura cobrada. Se generó automáticamente el ingreso correspondiente.'
                    : 'Factura marcada como cobrada.',
                'factura' => $factura->fresh(),
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar el número/serie de la factura manualmente (en cualquier estado salvo anulada).
     */
    public function actualizarNumero(Request $request, Factura $factura): JsonResponse
    {
        try {
            if ($factura->estado === 'anulada') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar el número de una factura anulada.',
                ], 400);
            }

            $validated = $request->validate([
                'serie' => 'nullable|string|max:20',
                'numero' => ['nullable', 'string', 'max:50', Rule::unique('facturas', 'numero')->ignore($factura->id)],
            ]);

            $factura->update([
                'serie' => $validated['serie'] ?: $factura->serie,
                'numero' => $validated['numero'] ?: null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Número de factura actualizado correctamente.',
                'factura' => $factura->fresh(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
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
