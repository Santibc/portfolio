<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RoleRedirectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirigir al usuario autenticado al dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }
}
