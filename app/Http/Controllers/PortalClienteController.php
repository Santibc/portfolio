<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\SolicitudCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

/**
 * Controlador del Portal de Cliente
 * Fase 7: Permite a los clientes ver su historial, seguimiento y descargar documentos
 */
class PortalClienteController extends Controller
{
    /**
     * Obtener el cliente asociado al usuario autenticado
     */
    private function getClienteAutenticado(): ?Cliente
    {
        return Cliente::where('user_id', Auth::id())->first();
    }

    /**
     * Obtener todos los IDs de cliente asociados al usuario autenticado
     */
    private function getClienteIds(): array
    {
        return Cliente::where('user_id', Auth::id())->pluck('id')->toArray();
    }

    /**
     * Verificar que el cliente autenticado es propietario de la solicitud
     */
    private function verificarPropietario(SolicitudCotizacion $solicitud): void
    {
        $clienteIds = $this->getClienteIds();

        if (empty($clienteIds) || !in_array($solicitud->cliente_id, $clienteIds)) {
            abort(403, 'No tiene acceso a este pedido');
        }
    }

    /**
     * Dashboard principal del portal
     */
    public function dashboard()
    {
        $cliente = $this->getClienteAutenticado();
        $clienteIds = $this->getClienteIds();

        if (!$cliente) {
            // En lugar de redirigir a login (causa loop), mostrar vista informativa
            return view('portal.sin-cliente', [
                'mensaje' => 'Su cuenta de usuario no tiene un cliente asociado. Por favor, contacte al administrador para vincular su cuenta.'
            ]);
        }

        // Métricas del cliente (incluye todos los clientes vinculados al usuario)
        $totalCotizaciones = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)->count();
        $cotizacionesPendientes = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)
            ->where('estado', SolicitudCotizacion::ESTADO_PENDIENTE)
            ->count();
        $pedidosEnCamino = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)
            ->where('estado', SolicitudCotizacion::ESTADO_APLICADA)
            ->whereIn('estado_envio', [
                SolicitudCotizacion::ENVIO_DESPACHADO,
                SolicitudCotizacion::ENVIO_EN_TRANSITO
            ])
            ->count();
        $totalComprado = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)
            ->where('estado', SolicitudCotizacion::ESTADO_APLICADA)
            ->sum('monto_total');

        // Últimos 5 pedidos
        $ultimosPedidos = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Alertas: pedidos despachados recientes (últimos 7 días)
        $pedidosDespachados = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)
            ->where('estado_envio', SolicitudCotizacion::ENVIO_DESPACHADO)
            ->where('despachado_en', '>=', now()->subDays(7))
            ->get();

        return view('portal.dashboard', compact(
            'cliente',
            'totalCotizaciones',
            'cotizacionesPendientes',
            'pedidosEnCamino',
            'totalComprado',
            'ultimosPedidos',
            'pedidosDespachados'
        ));
    }

    /**
     * Historial de pedidos con DataTables
     */
    public function historial(Request $request)
    {
        $cliente = $this->getClienteAutenticado();
        $clienteIds = $this->getClienteIds();

        if (!$cliente) {
            // En lugar de redirigir a login (causa loop), mostrar vista informativa
            return view('portal.sin-cliente', [
                'mensaje' => 'Su cuenta de usuario no tiene un cliente asociado. Por favor, contacte al administrador para vincular su cuenta.'
            ]);
        }

        if ($request->ajax()) {
            $query = SolicitudCotizacion::whereIn('cliente_id', $clienteIds)
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('fecha', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('monto_formateado', function ($row) {
                    return '$' . number_format($row->monto_total, 0, ',', '.');
                })
                ->addColumn('estado_badge', function ($row) {
                    $color = $row->color_estado;
                    $label = ucfirst($row->estado);
                    return "<span class=\"badge bg-{$color}\">{$label}</span>";
                })
                ->addColumn('pago_badge', function ($row) {
                    $color = $row->color_estado_pago;
                    $label = $row->etiqueta_estado_pago;
                    return "<span class=\"badge bg-{$color}\">{$label}</span>";
                })
                ->addColumn('envio_badge', function ($row) {
                    $color = $row->color_estado_envio;
                    $label = $row->etiqueta_estado_envio;
                    return "<span class=\"badge bg-{$color}\">{$label}</span>";
                })
                ->addColumn('acciones', function ($row) {
                    $btnDetalle = '<a href="' . route('portal.pedido.detalle', $row->id) . '" class="btn btn-sm btn-outline-primary" title="Ver detalle"><i class="bi bi-eye"></i></a>';
                    $btnSeguimiento = '<a href="' . route('portal.pedido.seguimiento', $row->id) . '" class="btn btn-sm btn-outline-info" title="Seguimiento"><i class="bi bi-geo-alt"></i></a>';

                    $btnGuia = '';
                    if ($row->puedeDescargarGuia()) {
                        $btnGuia = '<a href="' . route('portal.pedido.guia', $row->id) . '" class="btn btn-sm btn-outline-success" title="Descargar guía"><i class="bi bi-file-earmark-arrow-down"></i></a>';
                    }

                    $btnFactura = '';
                    if ($row->puedeDescargarFactura()) {
                        $btnFactura = '<a href="' . route('portal.pedido.factura', $row->id) . '" class="btn btn-sm btn-outline-warning" title="Descargar factura"><i class="bi bi-receipt"></i></a>';
                    }

                    return '<div class="btn-group">' . $btnDetalle . $btnSeguimiento . $btnGuia . $btnFactura . '</div>';
                })
                ->rawColumns(['estado_badge', 'pago_badge', 'envio_badge', 'acciones'])
                ->make(true);
        }

        return view('portal.historial', compact('cliente'));
    }

    /**
     * Ver detalle de un pedido
     */
    public function detallePedido(SolicitudCotizacion $solicitud)
    {
        $this->verificarPropietario($solicitud);

        $solicitud->load(['items.producto', 'items.varianteProducto', 'createdBy']);

        return view('portal.detalle', compact('solicitud'));
    }

    /**
     * Ver seguimiento de envío
     */
    public function seguimiento(SolicitudCotizacion $solicitud)
    {
        $this->verificarPropietario($solicitud);

        return view('portal.seguimiento', compact('solicitud'));
    }

    /**
     * Descargar guía de envío
     */
    public function descargarGuia(SolicitudCotizacion $solicitud)
    {
        $this->verificarPropietario($solicitud);

        if (!$solicitud->puedeDescargarGuia()) {
            return back()->with('error', 'La guía de envío no está disponible aún.');
        }

        $rutaArchivo = $solicitud->archivo_guia;

        // Buscar en public/
        $rutaPublica = public_path($rutaArchivo);
        if (file_exists($rutaPublica)) {
            return response()->download($rutaPublica, "guia-{$solicitud->numero_solicitud}.pdf");
        }

        return back()->with('error', 'No se encontró el archivo de la guía.');
    }

    /**
     * Descargar factura
     */
    public function descargarFactura(SolicitudCotizacion $solicitud)
    {
        $this->verificarPropietario($solicitud);

        if (!$solicitud->puedeDescargarFactura()) {
            return back()->with('error', 'La factura no está disponible aún.');
        }

        $rutaArchivo = $solicitud->archivo_factura;

        // Buscar en public/
        $rutaPublica = public_path($rutaArchivo);
        if (file_exists($rutaPublica)) {
            return response()->download($rutaPublica, "factura-{$solicitud->numero_factura}.pdf");
        }

        return back()->with('error', 'No se encontró el archivo de la factura.');
    }
}
