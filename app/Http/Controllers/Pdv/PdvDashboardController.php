<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\VentaPdv;
use App\Models\Prefactura;
use App\Models\ValeCaja;
use App\Services\CajaService;
use Illuminate\Http\Request;

class PdvDashboardController extends Controller
{
    protected CajaService $cajaService;

    public function __construct(CajaService $cajaService)
    {
        $this->cajaService = $cajaService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Find active session for this user
        $sesionActiva = $this->cajaService->obtenerSesionActivaDeUsuario($user->id);

        // Get cajas accessible to this user
        $cajas = Caja::activas()->with('ubicacion', 'cajeroAsignado')->get();

        // Dashboard data
        $data = [
            'sesionActiva' => $sesionActiva,
            'cajas' => $cajas,
        ];

        if ($user->hasRole(['admin'])) {
            // Admin sees all cajas, all sessions, all metrics
            $data['cajasAbiertas'] = Caja::abiertas()->with(['sesiones' => fn($q) => $q->abiertas()->with('usuario')])->get();
            $data['totalVentasHoy'] = VentaPdv::completadas()->delDia()->sum('total');
            $data['cantidadVentasHoy'] = VentaPdv::completadas()->delDia()->count();
            $data['prefacturasPendientes'] = Prefactura::pendientes()->count();
            $data['sesionesAbiertas'] = SesionCaja::abiertas()->with('caja', 'usuario')->get();
        }

        if ($user->hasRole(['cajero_principal'])) {
            // Cashier sees their session data + pending prefacturas
            if ($sesionActiva) {
                $data['ventasSesion'] = VentaPdv::where('sesion_caja_id', $sesionActiva->id)->completadas()->count();
                $data['totalSesion'] = VentaPdv::where('sesion_caja_id', $sesionActiva->id)->completadas()->sum('total');
                $data['prefacturasPendientes'] = Prefactura::pendientes()
                    ->where('ubicacion_id', $sesionActiva->caja->ubicacion_id)
                    ->count();
                $data['valesSesion'] = ValeCaja::where('sesion_caja_id', $sesionActiva->id)->activos()->sum('monto');
            }
        }

        if ($user->hasRole(['auxiliar_venta', 'vendedor'])) {
            $data['misPrefacturas'] = Prefactura::where('usuario_creador_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('pdv.dashboard', $data);
    }
}
