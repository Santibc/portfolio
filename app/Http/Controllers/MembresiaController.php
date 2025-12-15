<?php

namespace App\Http\Controllers;

use App\Models\PlanMembresia;
use App\Models\Membresia;
use App\Models\PagoMembresia;
use App\Services\WompiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MembresiaController extends Controller
{
    /**
     * Mostrar planes disponibles
     */
    public function index()
    {
        $planes = PlanMembresia::activos()->get();
        $empresa = Auth::user()->empresa()->with('planMembresia')->first();
        $membresiaActiva = null;

        // Si el usuario tiene empresa, verificar y asignar membresía gratuita si no tiene ninguna
        if ($empresa) {
            $membresiaActiva = $empresa->membresiaActiva;

            if (!$membresiaActiva) {
                // Asignar membresía gratuita automáticamente
                $this->asignarMembresiaGratuitaAutomatica($empresa);

                // Recargar la empresa con la nueva membresía
                $empresa->refresh();
                $empresa->load('planMembresia');
                $membresiaActiva = $empresa->membresiaActiva;
            }
        }

        return view('membresias.index', compact('planes', 'empresa', 'membresiaActiva'));
    }

    /**
     * Asignar membresía gratuita automáticamente a una empresa
     */
    private function asignarMembresiaGratuitaAutomatica($empresa)
    {
        $planGratuito = PlanMembresia::planGratuito();

        if (!$planGratuito) {
            // Si no hay plan gratuito, buscar por precio 0
            $planGratuito = PlanMembresia::where('precio', 0)->first();
        }

        if ($planGratuito) {
            DB::beginTransaction();

            try {
                // Crear membresía gratuita
                Membresia::create([
                    'empresa_id' => $empresa->id,
                    'plan_membresia_id' => $planGratuito->id,
                    'estado' => 'activa',
                    'precio_pagado' => 0,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null // Plan gratuito no expira
                ]);

                // Actualizar empresa con el plan
                $empresa->update(['plan_membresia_id' => $planGratuito->id]);

                DB::commit();

                Log::info("Membresía gratuita asignada automáticamente a empresa ID: {$empresa->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error al asignar membresía gratuita automática: " . $e->getMessage());
            }
        } else {
            Log::warning("No se encontró plan gratuito para asignar a empresa ID: {$empresa->id}");
        }
    }

    /**
     * Mostrar detalle de un plan
     */
    public function show($slug)
    {
        $plan = PlanMembresia::where('slug', $slug)->firstOrFail();
        $empresa = Auth::user()->empresa;

        // Si tiene empresa y es el plan actual, redirigir
        if ($empresa && $empresa->plan_membresia_id == $plan->id) {
            return redirect()->route('membresias.index')
                ->with('info', 'Ya tienes este plan activo');
        }

        return view('membresias.show', compact('plan', 'empresa'));
    }

    /**
     * Procesar compra de membresía
     */
    public function comprar(Request $request, $planId)
    {
        $plan = PlanMembresia::findOrFail($planId);
        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return redirect()->route('empresa.form')
                ->with('desde_membresias', true)
                ->with('warning', 'Para adquirir un plan de membresía, primero debes crear tu empresa.');
        }

        // Validar que no sea el plan actual
        if ($empresa->plan_membresia_id == $plan->id) {
            return back()->with('error', 'Ya tienes este plan activo');
        }
        
        // Si es plan gratuito, activar directamente
        if ($plan->esGratuito()) {
            return $this->activarPlanGratuito($empresa, $plan);
        }
        
        DB::beginTransaction();
        
        try {
            // Crear membresía pendiente
            $membresia = Membresia::create([
                'empresa_id' => $empresa->id,
                'plan_membresia_id' => $plan->id,
                'estado' => 'pendiente',
                'precio_pagado' => $plan->precio
            ]);
            
            // Crear registro de pago
            $referencia = 'MEM-' . strtoupper(Str::random(10));
            $pagoMembresia = PagoMembresia::create([
                'empresa_id' => $empresa->id,
                'membresia_id' => $membresia->id,
                'monto' => $plan->precio,
                'referencia_pago' => $referencia,
                'estado' => 'pendiente'
            ]);
            
            DB::commit();
            
            // Generar datos para Wompi
            $wompiService = new WompiService();
            $datosCheckout = $this->generarDatosCheckoutMembresia($plan, $empresa, $pagoMembresia);
            
            return view('membresias.redirect-wompi', compact('datosCheckout'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error procesando membresía: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el pago. Por favor intente nuevamente.');
        }
    }

    /**
     * Confirmar pago de membresía
     */
    public function confirmarPago(Request $request, $referencia)
    {
        $pagoMembresia = PagoMembresia::where('referencia_pago', $referencia)->firstOrFail();
        
        // Verificar si ya fue procesado
        if ($pagoMembresia->estado !== 'pendiente') {
            return $this->mostrarResultadoPago($pagoMembresia);
        }
        
        // Obtener ID de transacción de Wompi
        $transaccionWompiId = $request->get('id');
        
        if ($transaccionWompiId) {
            $wompiService = new WompiService();
            $datosTransaccion = $wompiService->consultarTransaccion($transaccionWompiId);
            
            if ($datosTransaccion) {
                $estado = $datosTransaccion['status'] ?? null;
                
                switch ($estado) {
                    case 'APPROVED':
                        $pagoMembresia->aprobar($datosTransaccion);
                        break;
                        
                    case 'DECLINED':
                    case 'VOIDED':
                        $pagoMembresia->rechazar($datosTransaccion['status_message'] ?? 'Pago rechazado');
                        break;
                }
            }
        }
        
        return $this->mostrarResultadoPago($pagoMembresia);
    }

    /**
     * Cancelar membresía
     */
    public function cancelar(Request $request)
    {
        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return redirect()->route('empresa.form')
                ->with('warning', 'Primero debes crear tu empresa.');
        }

        $membresiaActiva = $empresa->membresiaActiva;

        if (!$membresiaActiva) {
            return back()->with('error', 'No tienes una membresía activa');
        }
        
        $request->validate([
            'confirmar' => 'required|accepted'
        ]);
        
        $membresiaActiva->cancelar();
        
        return redirect()->route('membresias.index')
            ->with('success', 'Tu membresía ha sido cancelada. Ahora estás en el plan gratuito.');
    }

    /**
     * Historial de membresías
     */
    public function historial()
    {
        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return redirect()->route('empresa.form')
                ->with('warning', 'Primero debes crear tu empresa para ver el historial.');
        }

        $membresias = $empresa->membresias()
            ->with('plan', 'pagos')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('membresias.historial', compact('membresias'));
    }

    /**
     * Activar plan gratuito
     */
    private function activarPlanGratuito($empresa, $plan)
    {
        DB::beginTransaction();
        
        try {
            // Cancelar membresía actual si existe
            if ($empresa->membresiaActiva) {
                $empresa->membresiaActiva->cancelar();
            }
            
            // Crear nueva membresía gratuita
            $membresia = Membresia::create([
                'empresa_id' => $empresa->id,
                'plan_membresia_id' => $plan->id,
                'estado' => 'activa',
                'precio_pagado' => 0,
                'fecha_inicio' => now(),
                'fecha_fin' => null // Plan gratuito no expira
            ]);
            
            // Actualizar empresa
            $empresa->update(['plan_membresia_id' => $plan->id]);
            
            DB::commit();
            
            return redirect()->route('membresias.index')
                ->with('success', 'Has cambiado al plan gratuito exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error activando plan gratuito: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar de plan');
        }
    }

    /**
     * Generar datos para checkout de membresía
     */
    private function generarDatosCheckoutMembresia($plan, $empresa, $pagoMembresia)
    {
        $wompiService = new WompiService();
        $montoEnCentavos = intval($plan->precio * 100);
        
        // Generar firma
        $firma = $wompiService->generarFirmaIntegridad(
            $pagoMembresia->referencia_pago,
            $montoEnCentavos,
            'COP'
        );
        
        // URL de redirección
        $redirectUrl = route('membresias.pago.confirmacion', [
            'referencia' => $pagoMembresia->referencia_pago
        ]);
        
        $config = \App\Models\ConfiguracionPasarela::obtenerConfiguracionActiva('wompi');
        
        return [
            'public_key' => $config->public_key,
            'currency' => 'COP',
            'amount_in_cents' => $montoEnCentavos,
            'reference' => $pagoMembresia->referencia_pago,
            'signature_integrity' => $firma,
            'redirect_url' => $redirectUrl,
            'customer_email' => Auth::user()->email,
            'customer_full_name' => Auth::user()->name,
            'description' => "Membresía {$plan->nombre} - {$empresa->nombre}",
            'action_url' => 'https://checkout.wompi.co/p/'
        ];
    }

    /**
     * Mostrar resultado del pago
     */
    private function mostrarResultadoPago($pagoMembresia)
    {
        $pagoMembresia->load('membresia.plan', 'empresa');
        
        switch ($pagoMembresia->estado) {
            case 'aprobado':
                return view('membresias.pago-exitoso', compact('pagoMembresia'));
            case 'rechazado':
                return view('membresias.pago-rechazado', compact('pagoMembresia'));
            default:
                return view('membresias.pago-pendiente', compact('pagoMembresia'));
        }
    }
}