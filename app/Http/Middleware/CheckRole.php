<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Middleware para verificar roles del usuario.
     *
     * Uso en rutas:
     * Route::get('/admin', ...)->middleware('role:admin');
     * Route::get('/ventas', ...)->middleware('role:admin,vendedor');
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles - Roles permitidos separados por coma
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Si no hay usuario autenticado, redirigir a login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Verificar si el usuario tiene alguno de los roles permitidos
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si no tiene ningún rol permitido, abortar con 403
        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
