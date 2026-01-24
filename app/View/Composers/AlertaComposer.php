<?php

namespace App\View\Composers;

use App\Services\AlertaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertaComposer
{
    protected AlertaService $alertaService;

    public function __construct(AlertaService $alertaService)
    {
        $this->alertaService = $alertaService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $alertasNoLeidas = 0;

        if (Auth::check()) {
            $user = Auth::user();
            $roles = $user->getRoleNames()->toArray();
            $alertasNoLeidas = $this->alertaService->contarNoLeidas($user->id, $roles);
        }

        $view->with('alertasNoLeidas', $alertasNoLeidas);
    }
}
