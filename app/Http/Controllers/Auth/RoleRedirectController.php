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
     * Redirigir al usuario autenticado al dashboard segun su rol.
     */
    public function redirect(): RedirectResponse
    {
        $user = Auth::user();
        $dashboardRoute = $this->roleService->getDashboardRoute($user);

        return redirect()->route($dashboardRoute);
    }
}
