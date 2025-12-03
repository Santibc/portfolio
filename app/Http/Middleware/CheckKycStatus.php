<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckKycStatus
{
    /**
     * Handle an incoming request.
     *
     * Verifica si el usuario tiene su KYC aprobado.
     * Este middleware se usa para acciones que requieren verificación KYC,
     * como inversiones, retiros, etc.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar si el usuario tiene KYC aprobado
        if ($user->kyc_status !== 'aprobado') {
            // Si es una petición AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Debes completar y aprobar tu proceso KYC para realizar esta acción.',
                    'kyc_status' => $user->kyc_status
                ], 403);
            }

            // Si es una petición web, redirigir al KYC
            return redirect()
                ->route('kyc.index')
                ->with('warning', 'Debes completar y aprobar tu proceso KYC para realizar esta acción.');
        }

        return $next($request);
    }
}
