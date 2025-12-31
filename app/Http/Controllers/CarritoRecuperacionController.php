<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CarritoAbandonadoLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CarritoRecuperacionController extends Controller
{
    /**
     * Recuperar carrito desde email de abandono
     */
    public function recuperar(Request $request, string $token)
    {
        // Buscar el log por token
        $log = CarritoAbandonadoLog::where('token_recuperacion', $token)->first();

        if (!$log) {
            return redirect()->route('home')
                ->with('error', 'El enlace de recuperación no es válido o ha expirado.');
        }

        // Marcar como clickeado
        $log->marcarClickeado();

        // Obtener el carrito original
        $carritoOriginal = $log->carrito;

        if (!$carritoOriginal || empty($carritoOriginal->items)) {
            return redirect()->route('home')
                ->with('error', 'El carrito ya no está disponible.');
        }

        // Obtener o crear carrito para la sesión actual
        $carritoActual = Carrito::obtenerOCrear(Session::getId(), $carritoOriginal->empresa_id);

        // Transferir items del carrito abandonado al carrito actual
        $itemsOriginales = $carritoOriginal->items ?? [];
        $itemsActuales = $carritoActual->items ?? [];

        foreach ($itemsOriginales as $key => $item) {
            if (!isset($itemsActuales[$key])) {
                $itemsActuales[$key] = $item;
            }
        }

        $carritoActual->items = $itemsActuales;

        // Transferir adicionales si los hay
        if (!empty($carritoOriginal->adicionales)) {
            $adicionales = $carritoActual->adicionales ?? [];
            foreach ($carritoOriginal->adicionales as $key => $adicional) {
                if (!isset($adicionales[$key])) {
                    $adicionales[$key] = $adicional;
                }
            }
            $carritoActual->adicionales = $adicionales;
        }

        // Transferir mensaje de tarjeta
        if ($carritoOriginal->mensaje_tarjeta && !$carritoActual->mensaje_tarjeta) {
            $carritoActual->mensaje_tarjeta = $carritoOriginal->mensaje_tarjeta;
        }

        // Aplicar descuento si es el tercer recordatorio
        if ($log->recordatorio_numero >= 3 && !$carritoActual->codigo_descuento) {
            // Aplicar código de descuento especial
            $carritoActual->codigo_descuento = 'VUELVE10';
        }

        // Guardar UTM del carrito original si existe
        $carritoActual->utm_source = $carritoOriginal->utm_source ?? 'email';
        $carritoActual->utm_medium = $carritoOriginal->utm_medium ?? 'abandoned_cart';
        $carritoActual->utm_campaign = $carritoOriginal->utm_campaign ?? "reminder_{$log->recordatorio_numero}";

        $carritoActual->recalcularTotales();

        // Marcar el carrito original como recuperado
        $log->marcarRecuperado();

        return redirect()->route('tienda.carrito')
            ->with('success', '¡Bienvenido de vuelta! Tu carrito ha sido restaurado.');
    }

    /**
     * Tracking pixel para email abierto
     */
    public function trackApertura(string $token)
    {
        $log = CarritoAbandonadoLog::where('token_recuperacion', $token)->first();

        if ($log && $log->estado === 'enviado') {
            $log->marcarAbierto();
        }

        // Retornar un pixel transparente 1x1
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
