<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * El rol "ventas" solo puede usar la caja (abrir/cerrar turno y registrar ventas) y su perfil.
 * Va en el grupo `web`, así cubre todas las rutas sin tener que marcar cada una; los demás
 * usuarios no se ven afectados.
 */
class RestringirRolVentas
{
    /** Rutas (patrones de nombre) permitidas para el rol ventas. */
    private const RUTAS_PERMITIDAS = [
        'caja.index',
        'caja.turno.abrir',
        'caja.turno.cerrar',
        'caja.venta.store',
        'profile.*',
        'logout',
        'password.*',
        'verification.*',
    ];

    /** Al entrar aquí (ej. después del login) se manda a la caja sin mostrar error. */
    private const RUTAS_INICIO = ['dashboard', 'welcome'];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user === null || ! $user->esVendedor() || $request->routeIs(...self::RUTAS_PERMITIDAS)) {
            return $next($request);
        }

        if (! $request->isMethod('GET')) {
            abort(403, 'Tu usuario solo tiene acceso a la caja.');
        }

        $redirect = redirect()->route('caja.index');

        return $request->routeIs(...self::RUTAS_INICIO)
            ? $redirect
            : $redirect->with('error', 'Tu usuario solo tiene acceso a la caja.');
    }
}
