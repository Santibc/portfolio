<?php

namespace App\Http\Controllers\Facturacion;

use App\Exports\PlantillaImportacionFacturaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Facturacion\FacturaRequest;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Impuesto;
use App\Models\Moneda;
use App\Models\PlantillaFactura;
use App\Models\Producto;
use App\Models\Talla;
use App\Services\Facturacion\FacturaItemImportService;
use App\Services\Facturacion\FacturaService;
use App\Services\Facturacion\TasaCambioService;
use App\Services\Facturacion\TemplateRenderer;
use App\Services\Siigo\SiigoEmisionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FacturaController extends Controller
{
    public function __construct(
        private readonly FacturaService $service,
        private readonly TemplateRenderer $renderer,
        private readonly SiigoEmisionService $siigo,
        private readonly FacturaItemImportService $importador,
    ) {}

    public function index(Request $request): View
    {
        $query = Factura::with(['cliente', 'moneda'])->orderByDesc('fecha')->orderByDesc('id');

        if ($estado = $request->string('estado')->toString()) {
            $query->where('estado', $estado);
        }
        if ($cliente = $request->integer('cliente_id')) {
            $query->where('cliente_id', $cliente);
        }
        if ($desde = $request->string('desde')->toString()) {
            $query->whereDate('fecha', '>=', $desde);
        }
        if ($hasta = $request->string('hasta')->toString()) {
            $query->whereDate('fecha', '<=', $hasta);
        }
        if ($request->filled('es_electronica')) {
            $query->where('es_electronica', $request->boolean('es_electronica'));
        }

        $facturas = $query->paginate(20)->withQueryString();

        return view('facturacion.facturas.index', [
            'facturas' => $facturas,
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre']),
            'filtros' => $request->only(['estado', 'cliente_id', 'desde', 'hasta', 'es_electronica']),
        ]);
    }

    public function create(): View
    {
        return view('facturacion.facturas.form', $this->formData(new Factura(['fecha' => now()->toDateString(), 'items' => []])));
    }

    public function store(FacturaRequest $request): RedirectResponse
    {
        $factura = $this->service->crearBorrador(
            $request->validated(),
            $request->input('items', []),
            $request->user()?->id,
        );

        return redirect()->route('facturacion.facturas.edit', $factura)->with('success', 'Borrador creado.');
    }

    public function edit(Factura $factura): View
    {
        $factura->load(['items', 'cliente', 'moneda']);

        return view('facturacion.facturas.form', $this->formData($factura));
    }

    public function update(FacturaRequest $request, Factura $factura): RedirectResponse
    {
        $this->service->actualizarBorrador(
            $factura,
            $request->validated(),
            $request->input('items', []),
        );

        return redirect()->route('facturacion.facturas.edit', $factura)->with('success', 'Factura actualizada.');
    }

    public function destroy(Factura $factura): RedirectResponse
    {
        if ($factura->yaEmitida()) {
            return back()->with('error', 'No puedes eliminar una factura emitida. Anúlala primero.');
        }

        $factura->delete();

        return redirect()->route('facturacion.facturas.index')->with('success', 'Factura eliminada.');
    }

    public function emitir(Factura $factura): RedirectResponse
    {
        $this->service->emitirNoElectronica($factura);

        return back()->with('success', 'Factura emitida con consecutivo interno '.$factura->numero_interno.'.');
    }

    public function anular(Factura $factura): RedirectResponse
    {
        if (! $factura->yaEmitida()) {
            return back()->with('error', 'La factura no está emitida.');
        }

        $factura->estado = 'anulada';
        $factura->save();

        return back()->with('success', 'Factura anulada.');
    }

    /**
     * Actualiza los datos de envío (PO, AWB, Shipper) de una factura.
     * A diferencia de otros campos, estos pueden editarse incluso después
     * de emitida la factura porque suelen obtenerse días después.
     */
    public function updateDatosEnvio(Request $request, Factura $factura): RedirectResponse
    {
        $data = $request->validate([
            'po_numero' => ['nullable', 'string', 'max:60'],
            'awb' => ['nullable', 'string', 'max:60'],
            'shipper' => ['nullable', 'string', 'max:100'],
        ]);

        $factura->update($data);

        return back()->with('success', 'Datos de envío actualizados.');
    }

    /**
     * Emite la factura electrónicamente ante la DIAN usando Siigo API.
     * El QR se genera localmente según el Anexo Técnico DIAN 1.9 con el CUFE recibido.
     */
    public function emitirElectronica(Factura $factura): RedirectResponse
    {
        $factura->load(['items', 'cliente', 'moneda']);

        try {
            $this->siigo->emitir($factura);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Ocurrió un error inesperado al emitir la factura electrónica.');
        }

        if ($factura->fresh()->estado === 'emitida') {
            return back()->with('success', "Factura emitida electrónicamente. CUFE: {$factura->cufe}");
        }

        return back()->with('error', 'Siigo procesó la factura pero la DIAN no la aceptó. Revisa el log siigo para ver los errores.');
    }

    public function pdf(Factura $factura): Response
    {
        $factura->load(['items', 'cliente', 'moneda', 'plantilla']);

        $plantilla = $factura->plantilla ?? PlantillaFactura::query()->default()->first();

        if (! $plantilla) {
            abort(422, 'No hay plantilla de factura configurada.');
        }

        $html = $this->renderer->render($plantilla, $this->datosFactura($factura));

        $pdf = Pdf::loadHTML($html)->setPaper('letter');

        $directorio = public_path('uploads/facturas');
        if (! File::isDirectory($directorio)) {
            File::makeDirectory($directorio, 0755, true);
        }

        $rutaAbs = $directorio.DIRECTORY_SEPARATOR.$factura->numero_interno.'.pdf';
        $pdf->save($rutaAbs);

        $factura->pdf_path = 'uploads/facturas/'.$factura->numero_interno.'.pdf';
        $factura->save();

        return $pdf->download($factura->numero_interno.'.pdf');
    }

    public function previsualizar(Factura $factura): Response
    {
        $factura->load(['items', 'cliente', 'moneda', 'plantilla']);
        $plantilla = $factura->plantilla ?? PlantillaFactura::query()->default()->first();

        if (! $plantilla) {
            abort(422, 'No hay plantilla configurada.');
        }

        $html = $this->renderer->render($plantilla, $this->datosFactura($factura));

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Descarga una plantilla Excel para importar líneas de factura. Incluye
     * hasta 3 referencias reales del catálogo como ejemplo.
     */
    public function plantillaImportacion(): BinaryFileResponse
    {
        $tallas = Talla::activas()->orderBy('orden')->orderBy('nombre')->pluck('nombre')->all();
        // Celdas vacías para cada columna de talla en las filas de ejemplo.
        $tallasVacias = array_fill(0, count($tallas), '');

        $ejemplos = Producto::where('activo', true)
            ->orderBy('referencia')
            ->take(3)
            ->get()
            ->map(fn (Producto $p) => [$p->referencia, ...$tallasVacias, 1, (float) $p->precio_unitario, 0, 'valor', 19])
            ->all();

        return Excel::download(new PlantillaImportacionFacturaExport($ejemplos, $tallas), 'plantilla-lineas-factura.xlsx');
    }

    /**
     * Procesa un Excel de líneas y devuelve JSON con los ítems válidos (listos
     * para el formulario) y la lista de errores por fila. No persiste nada:
     * el usuario revisa y guarda la factura normalmente.
     */
    public function importarItems(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
            'iva_default' => ['nullable', 'numeric', 'min:0'],
        ]);

        $resultado = $this->importador->procesar(
            (string) $request->file('archivo')->getRealPath(),
            (float) ($datos['iva_default'] ?? 19),
        );

        return response()->json($resultado);
    }

    /**
     * Devuelve la tasa de cambio (COP por unidad de moneda) para una moneda y
     * fecha dadas. Lo consume el formulario para autocompletar el campo
     * `tasa_cambio` al elegir una moneda distinta de COP. Si la fuente falla,
     * responde 422 con un mensaje y el usuario digita la tasa manualmente.
     */
    public function tasaCambio(Request $request, TasaCambioService $tasaCambio): JsonResponse
    {
        $datos = $request->validate([
            'moneda' => ['required', 'string', 'max:10'],
            'fecha' => ['nullable', 'date'],
        ]);

        try {
            $resultado = $tasaCambio->obtener(
                $datos['moneda'],
                $datos['fecha'] ?? now()->toDateString(),
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($resultado);
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Factura $factura): array
    {
        // Productos activos + los ya referenciados por esta factura (aunque estén
        // inactivos), para que el select de líneas no pierda la selección al editar.
        $idsEnFactura = $factura->relationLoaded('items')
            ? $factura->items->pluck('producto_id')->filter()->all()
            : [];

        return [
            'factura' => $factura,
            'clientes' => Cliente::orderBy('nombre')->get(),
            'productos' => Producto::query()
                ->where(fn ($q) => $q->where('activo', true)->orWhereIn('id', $idsEnFactura))
                ->orderBy('referencia')
                ->get(),
            'monedas' => Moneda::activas()->orderBy('codigo')->get(),
            'impuestos' => Impuesto::where('activo', true)->orderBy('porcentaje')->get(),
            'plantillas' => PlantillaFactura::activas()->orderByDesc('es_default')->get(),
            'tallas' => Talla::activas()->orderBy('orden')->orderBy('nombre')->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function datosFactura(Factura $factura): array
    {
        $simbolo = match ($factura->moneda?->codigo) {
            'EUR' => '€',
            'USD' => 'US$',
            'COP' => '$',
            default => (string) ($factura->moneda?->simbolo ?? ''),
        };

        return [
            'empresa' => app(\App\Services\Settings\EmpresaData::class)->paraPlantilla(),
            'cliente' => [
                'nombre' => (string) $factura->cliente?->nombre,
                'identificacion' => (string) $factura->cliente?->identificacion,
                'direccion_facturacion' => (string) $factura->cliente?->direccion_facturacion,
                'direccion_envio' => (string) $factura->cliente?->direccion_envio,
                'email' => (string) $factura->cliente?->email,
                'telefono' => (string) $factura->cliente?->telefono,
                'incoterm' => (string) $factura->cliente?->incoterm?->codigo,
                'puerto' => (string) $factura->cliente?->puerto?->nombre,
                'origen' => 'COLOMBIA',
                'destino' => (string) ($factura->cliente?->pais ?? ''),
            ],
            'factura' => [
                'numero' => $factura->numero_siigo ?? $factura->numero_interno,
                'fecha' => $factura->fecha->format('Y-m-d'),
                'vencimiento' => $factura->vencimiento?->format('Y-m-d'),
                'moneda' => (string) $factura->moneda?->codigo,
                'simbolo' => $simbolo,
                'cufe' => (string) $factura->cufe,
                'tasa_cambio' => $factura->tasa_cambio,
                'observaciones' => (string) $factura->observaciones,
                // QR lo llena SiigoEmisionService después del timbrado. Si no hay, se renderiza vacío.
                'qr' => (string) $factura->qr_url,
                'qr_html' => (string) $factura->qr_html,
                'po' => (string) $factura->po_numero,
                'awb' => (string) $factura->awb,
                'shipper' => (string) $factura->shipper,
                'cod' => '',
                'remision' => (string) $factura->remision,
                'payment_terms' => (string) $factura->payment_terms,
                'version' => 'V 1.9',
            ],
            'items' => $factura->items->map(fn ($item) => [
                'referencia' => (string) $item->referencia,
                'descripcion' => (string) $item->descripcion,
                'color' => (string) $item->color,
                'composicion' => (string) $item->composicion,
                'composition' => (string) $item->composicion,
                'size' => $this->formatearTallas($item->tallas_json),
                'codigo_pa' => (string) $item->codigo_pa,
                'pais_origen' => (string) $item->pais_origen,
                'country_of_origin' => (string) $item->pais_origen,
                'cantidad' => number_format((float) $item->cantidad, 0, ',', '.'),
                'precio_unitario' => number_format((float) $item->precio_unitario, 2, ',', '.'),
                'descuento' => number_format($item->descuentoValor(), 2, ',', '.'),
                'total' => number_format((float) $item->total_linea, 2, ',', '.'),
            ])->all(),
            'totales' => [
                'subtotal' => number_format((float) $factura->subtotal, 2, ',', '.'),
                'iva' => number_format((float) $factura->iva_total, 2, ',', '.'),
                'descuento' => number_format((float) $factura->descuento_total, 2, ',', '.'),
                'flete' => number_format((float) $factura->flete, 2, ',', '.'),
                'seguro' => number_format((float) $factura->seguro, 2, ',', '.'),
                'total' => number_format((float) $factura->total, 2, ',', '.'),
                'total_cop' => $factura->total_cop ? number_format((float) $factura->total_cop, 2, ',', '.') : '',
            ],
            'banco' => $this->datosBanco(),
            'contacto' => $this->datosContacto(),
        ];
    }

    /**
     * Datos bancarios para la plantilla. Remapea las claves planas de
     * configuración (`banco.nombre`) a la estructura anidada que espera el
     * renderer (`{{banco.nombre}}` → $data['banco']['nombre']).
     *
     * @return array<string, string>
     */
    private function datosBanco(): array
    {
        $config = app(\App\Services\Settings\ConfigService::class);

        return [
            'nombre' => (string) $config->get('banco.nombre', ''),
            'pais' => (string) $config->get('banco.pais', ''),
            'direccion' => (string) $config->get('banco.direccion', ''),
            'titular' => (string) $config->get('banco.titular', ''),
            'moneda' => (string) $config->get('banco.moneda', ''),
            'swift' => (string) $config->get('banco.swift', ''),
            'numero_cuenta' => (string) $config->get('banco.numero_cuenta', ''),
        ];
    }

    /**
     * Datos del contacto financiero. Las claves se guardan con el prefijo
     * `contacto_financiero.*` pero la plantilla las consume como `{{contacto.*}}`.
     *
     * @return array<string, string>
     */
    private function datosContacto(): array
    {
        $config = app(\App\Services\Settings\ConfigService::class);

        return [
            'nombre' => (string) $config->get('contacto_financiero.nombre', ''),
            'email' => (string) $config->get('contacto_financiero.email', ''),
            'telefono' => (string) $config->get('contacto_financiero.telefono', ''),
        ];
    }

    /**
     * Formatea el JSON de tallas para la columna SIZE de la plantilla.
     *
     * Formato actual: mapa {talla: cantidad} → "2L, 1XS, 3M" (cantidad + talla).
     * Compatibilidad legada: lista ["S","M","L"] → "S, M, L" (solo nombres).
     * Acepta array, string JSON o null.
     *
     * @param  mixed  $tallas
     */
    private function formatearTallas($tallas): string
    {
        if (is_string($tallas) && $tallas !== '') {
            $tallas = json_decode($tallas, true);
        }

        if (! is_array($tallas) || $tallas === []) {
            return '';
        }

        $partes = [];
        foreach ($tallas as $nombre => $cantidad) {
            // Mapa {talla: cantidad}: claves no numéricas con cantidad.
            if (is_string($nombre)) {
                $cant = (float) $cantidad;
                if ($cant > 0) {
                    $partes[] = (rtrim(rtrim(number_format($cant, 2, '.', ''), '0'), '.')).$nombre;
                }

                continue;
            }

            // Lista legada ["S","M","L"]: solo nombres.
            $valor = trim((string) $cantidad);
            if ($valor !== '') {
                $partes[] = $valor;
            }
        }

        return implode(', ', $partes);
    }
}
