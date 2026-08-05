<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    /**
     * Si el usuario autenticado quedó DESACTIVADO (activo = false), se cierra su sesión
     * en su siguiente petición: desactivar una cuenta lo saca al instante de la plataforma.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $mensaje = 'Usuario inactivo. Contacta al administrador.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $mensaje], 401);
            }

            return redirect()->route('login')->with('error', $mensaje);
        }

        return $next($request);
    }
}
