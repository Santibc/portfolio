<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Protege la API de solo lectura que consume la app móvil "Mis Negocios".
 * La app no tiene login: se autentica con una llave compartida en el header X-Api-Key.
 */
class VerificarLlaveAppMovil
{
    public function handle(Request $request, Closure $next): mixed
    {
        $llave    = (string) config('services.app_movil.key');
        $recibida = (string) $request->header('X-Api-Key', '');

        // Sin llave configurada la API queda cerrada (nunca abierta por omisión).
        if ($llave === '' || $recibida === '' || ! hash_equals($llave, $recibida)) {
            return new JsonResponse(['message' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
