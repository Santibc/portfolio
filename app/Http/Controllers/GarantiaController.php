<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Garantia;
use App\Models\GarantiaDocumento;
use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class GarantiaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Garantia::with([
                'cliente',
                'producto',
                'variante',
                'solicitud',
                'usuarioCreador',
                'usuarioLiberador',
                'documentos',
            ])->orderBy('created_at', 'desc');

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->fecha_hasta);
            }

            $puedeLiberar = auth()->user()->hasAnyRole(['admin', 'garantias']);

            return DataTables::of($query)
                ->addColumn('cliente_nombre', function ($row) {
                    return $row->cliente->nombre_completo ?? 'N/A';
                })
                ->addColumn('producto_nombre', function ($row) {
                    $nombre = $row->producto->nombre ?? 'N/A';
                    if ($row->variante) {
                        $sufijo = $row->variante->nombre_variante ?? null;
                        if ($sufijo) {
                            $nombre .= ' — ' . $sufijo;
                        }
                    }
                    return $nombre;
                })
                ->addColumn('tipo_badge', function ($row) {
                    $tipos = Garantia::tiposDisponibles();
                    $colores = [
                        Garantia::TIPO_CAMBIO_PRODUCTO => 'primary',
                        Garantia::TIPO_DESCUENTO => 'info',
                        Garantia::TIPO_NOTA_CREDITO => 'warning',
                        Garantia::TIPO_OTRO => 'secondary',
                    ];
                    $color = $colores[$row->tipo] ?? 'secondary';
                    $label = $tipos[$row->tipo] ?? $row->tipo;
                    return '<span class="badge bg-' . $color . '">' . e($label) . '</span>';
                })
                ->addColumn('estado_badge', function ($row) {
                    if ($row->estado === Garantia::ESTADO_PENDIENTE) {
                        return '<span class="badge bg-warning text-dark">Pendiente</span>';
                    }
                    return '<span class="badge bg-success">Liberado</span>';
                })
                ->addColumn('cotizacion_vinculada', function ($row) {
                    return $row->solicitud?->numero_solicitud ?? '—';
                })
                ->addColumn('fecha_creacion', function ($row) {
                    return $row->created_at?->format('d/m/Y H:i');
                })
                ->addColumn('action', function ($row) use ($puedeLiberar) {
                    $btns = '<div class="d-flex gap-1">';
                    $btns .= '<button type="button" class="btn btn-sm btn-outline-info" onclick="verGarantia(' . $row->id . ')" title="Ver"><i class="bi bi-eye"></i></button>';
                    if ($puedeLiberar && $row->estaPendiente()) {
                        $btns .= '<button type="button" class="btn btn-sm btn-outline-success" onclick="liberarGarantia(' . $row->id . ')" title="Liberar"><i class="bi bi-unlock"></i> Liberar</button>';
                    }
                    $btns .= '</div>';
                    return $btns;
                })
                ->rawColumns(['action', 'tipo_badge', 'estado_badge'])
                ->make(true);
        }

        $clientes = Cliente::activos()->orderBy('nombre_contacto')->get(['id', 'nombre_contacto', 'razon_social', 'tipo_cliente']);
        $tipos = Garantia::tiposDisponibles();

        return view('garantias.index', compact('clientes', 'tipos'));
    }

    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre_contacto')->get(['id', 'nombre_contacto', 'razon_social', 'tipo_cliente', 'numero_identificacion']);
        $tipos = Garantia::tiposDisponibles();

        return view('garantias.form', compact('clientes', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'required|exists:productos,id',
            'variante_producto_id' => 'nullable|exists:variantes_productos,id',
            'tipo' => 'required|in:cambio_producto,descuento,nota_credito,otro',
            'tipo_otro_descripcion' => 'required_if:tipo,otro|nullable|max:500',
            'documentos' => 'required|array|min:1',
            'documentos.*' => 'file|max:10240',
        ], [
            'documentos.required' => 'Debes adjuntar al menos un documento.',
            'documentos.*.max' => 'Cada documento no puede superar los 10MB.',
            'tipo_otro_descripcion.required_if' => 'Debes especificar el tipo cuando seleccionas "Otro".',
        ]);

        DB::beginTransaction();
        try {
            $garantia = Garantia::create([
                'cliente_id' => $request->cliente_id,
                'producto_id' => $request->producto_id,
                'variante_producto_id' => $request->variante_producto_id,
                'tipo' => $request->tipo,
                'tipo_otro_descripcion' => $request->tipo === Garantia::TIPO_OTRO ? $request->tipo_otro_descripcion : null,
                'estado' => Garantia::ESTADO_PENDIENTE,
                'usuario_creador_id' => auth()->id(),
            ]);

            $directorio = public_path('uploads/garantias/documentos/' . $garantia->id);
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
            }

            foreach ($request->file('documentos') as $archivo) {
                if (!$archivo || !$archivo->isValid()) {
                    throw new \Exception('Archivo inválido o subida fallida: ' . ($archivo?->getClientOriginalName() ?? 'desconocido'));
                }

                $nombreOriginal = $archivo->getClientOriginalName();
                $mimeType = $archivo->getClientMimeType() ?: 'application/octet-stream';
                $tamano = $archivo->getSize();
                $nombreSeguro = preg_replace('/[^A-Za-z0-9._-]/', '_', $nombreOriginal);
                $nombre = time() . '_' . uniqid() . '_' . $nombreSeguro;

                $rutaTemp = $archivo->getPathname();
                $rutaDestino = $directorio . DIRECTORY_SEPARATOR . $nombre;

                if (!file_exists($rutaTemp) || !is_readable($rutaTemp)) {
                    throw new \Exception('El archivo temporal no se encuentra disponible: ' . $rutaTemp);
                }

                if (!@copy($rutaTemp, $rutaDestino)) {
                    throw new \Exception('No se pudo guardar el archivo: ' . $nombreOriginal);
                }
                @unlink($rutaTemp);

                GarantiaDocumento::create([
                    'garantia_id' => $garantia->id,
                    'nombre_original' => $nombreOriginal,
                    'nombre_archivo' => $nombre,
                    'ruta_relativa' => 'uploads/garantias/documentos/' . $garantia->id . '/' . $nombre,
                    'mime_type' => $mimeType,
                    'tamano' => $tamano,
                ]);
            }

            DB::commit();

            return redirect()->route('garantias.index')->with('success', 'Garantía registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error al registrar garantía', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['documentos']),
            ]);
            return back()->withInput()->with('error', 'Error al registrar la garantía: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $garantia = Garantia::with([
            'cliente',
            'producto',
            'variante',
            'solicitud',
            'usuarioCreador',
            'usuarioLiberador',
            'documentos',
        ])->findOrFail($id);

        return response()->json([
            'id' => $garantia->id,
            'cliente' => $garantia->cliente?->nombre_completo,
            'producto' => $garantia->producto?->nombre,
            'variante' => $garantia->variante?->nombre_variante,
            'tipo' => $garantia->tipo,
            'tipo_legible' => $garantia->tipoLegible(),
            'tipo_otro_descripcion' => $garantia->tipo_otro_descripcion,
            'estado' => $garantia->estado,
            'observacion_liberacion' => $garantia->observacion_liberacion,
            'usuario_creador' => $garantia->usuarioCreador?->name,
            'usuario_liberador' => $garantia->usuarioLiberador?->name,
            'liberado_en' => $garantia->liberado_en?->format('d/m/Y H:i'),
            'created_at' => $garantia->created_at?->format('d/m/Y H:i'),
            'cotizacion' => $garantia->solicitud ? [
                'id' => $garantia->solicitud->id,
                'numero' => $garantia->solicitud->numero_solicitud,
            ] : null,
            'documentos' => $garantia->documentos->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'nombre_original' => $doc->nombre_original,
                    'tamano' => $doc->tamano,
                    'mime_type' => $doc->mime_type,
                    'url_descarga' => route('garantias.documentos.descargar', $doc->id),
                ];
            }),
        ]);
    }

    public function liberar(Request $request, $id)
    {
        $request->validate([
            'observacion_liberacion' => 'required|string|min:5|max:1000',
            'solicitud_cotizacion_id' => 'nullable|exists:solicitudes_cotizacion,id',
        ], [
            'observacion_liberacion.required' => 'La observación es obligatoria para liberar la garantía.',
            'observacion_liberacion.min' => 'La observación debe tener al menos 5 caracteres.',
        ]);

        $garantia = Garantia::findOrFail($id);

        if ($garantia->estaLiberada()) {
            return response()->json(['error' => 'La garantía ya está liberada.'], 422);
        }

        $garantia->update([
            'estado' => Garantia::ESTADO_LIBERADO,
            'observacion_liberacion' => $request->observacion_liberacion,
            'usuario_liberador_id' => auth()->id(),
            'liberado_en' => now(),
            'solicitud_cotizacion_id' => $request->solicitud_cotizacion_id ?: $garantia->solicitud_cotizacion_id,
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Garantía liberada correctamente.',
        ]);
    }

    public function descargarDocumento($id)
    {
        $documento = GarantiaDocumento::findOrFail($id);
        $rutaCompleta = public_path($documento->ruta_relativa);

        if (!File::exists($rutaCompleta)) {
            abort(404, 'El documento no existe.');
        }

        return response()->download($rutaCompleta, $documento->nombre_original);
    }

    public function buscarProductos(Request $request)
    {
        $termino = trim($request->get('q', ''));

        if (mb_strlen($termino) < 2) {
            return response()->json(['data' => []]);
        }

        $productos = Producto::activos()
            ->where(function ($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('referencia', 'like', "%{$termino}%")
                    ->orWhere('codigo_barras', 'like', "%{$termino}%")
                    ->orWhereHas('variantes', function ($q) use ($termino) {
                        $q->where('codigo_barras', 'like', "%{$termino}%")
                          ->orWhere('sku', 'like', "%{$termino}%")
                          ->orWhere('referencia_variante', 'like', "%{$termino}%")
                          ->orWhere('color', 'like', "%{$termino}%");
                    });
            })
            ->with(['variantes'])
            ->limit(40)
            ->get();

        $filas = [];
        foreach ($productos as $producto) {
            if ($producto->tiene_variantes) {
                if ($producto->variantes->isEmpty()) {
                    $producto->load('variantes');
                }
                foreach ($producto->variantes as $variante) {
                    $filas[] = [
                        'producto_id' => $producto->id,
                        'variante_producto_id' => $variante->id,
                        'referencia' => $variante->referencia_variante ?: $producto->referencia,
                        'nombre_completo' => $producto->nombre . ($variante->nombre_variante ? ' — ' . $variante->nombre_variante : ''),
                        'sku' => $variante->sku,
                        'codigo_barras' => $variante->codigo_barras ?: $producto->codigo_barras,
                        'tiene_variante' => true,
                    ];
                }
            } else {
                $filas[] = [
                    'producto_id' => $producto->id,
                    'variante_producto_id' => null,
                    'referencia' => $producto->referencia,
                    'nombre_completo' => $producto->nombre,
                    'sku' => null,
                    'codigo_barras' => $producto->codigo_barras,
                    'tiene_variante' => false,
                ];
            }
        }

        return response()->json(['data' => array_slice($filas, 0, 30)]);
    }

    public function garantiasPendientesCliente($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        $garantias = $cliente->garantiasPendientes()
            ->with(['producto', 'variante', 'documentos', 'usuarioCreador'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $garantias->map(function ($g) {
                return [
                    'id' => $g->id,
                    'producto' => $g->producto?->nombre,
                    'variante' => $g->variante?->nombre_variante,
                    'tipo' => $g->tipo,
                    'tipo_legible' => $g->tipoLegible(),
                    'fecha' => $g->created_at?->format('d/m/Y H:i'),
                    'usuario_creador' => $g->usuarioCreador?->name,
                    'documentos' => $g->documentos->map(fn($d) => [
                        'id' => $d->id,
                        'nombre_original' => $d->nombre_original,
                        'url_descarga' => route('garantias.documentos.descargar', $d->id),
                    ]),
                ];
            }),
        ]);
    }
}
