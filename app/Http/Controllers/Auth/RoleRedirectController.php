<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RoleRedirectController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->middleware('auth');
        $this->roleService = $roleService;
    }

    /**
     * Redirigir al usuario autenticado a su dashboard correspondiente según su rol.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        $user = Auth::user();

        // Obtener la ruta del dashboard según el rol
        $dashboardRoute = $this->roleService->getDashboardRoute($user);

        return redirect()->route($dashboardRoute);
    }
}
