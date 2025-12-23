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

        // Bloquear si NO ha subido documentos
        if ($user->kyc_status === 'pendiente') {
            // Si es una petición AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Debes subir tus documentos KYC antes de invertir. Una vez subidos podrás invertir mientras los revisamos.',
                    'kyc_status' => $user->kyc_status
                ], 403);
            }

            return redirect()
                ->route('inversionista.kyc.create')
                ->with('warning', 'Debes subir tus documentos KYC antes de invertir. Una vez subidos podrás invertir mientras los revisamos.');
        }

        // Bloquear si fue RECHAZADO
        if ($user->kyc_status === 'rechazado') {
            // Si es una petición AJAX, retornar JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu verificación KYC fue rechazada. Debes subir nuevos documentos para poder invertir.',
                    'motivo' => $user->kyc_notas,
                    'kyc_status' => $user->kyc_status
                ], 403);
            }

            return redirect()
                ->route('inversionista.kyc.create')
                ->with('error', 'Tu verificación KYC fue rechazada. Debes subir nuevos documentos para poder invertir. Motivo: ' . $user->kyc_notas);
        }

        // Estados que SÍ permiten invertir: en_revision, aprobado
        return $next($request);
    }
}
