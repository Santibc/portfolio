<?php

namespace App\Providers;

use App\View\Composers\AlertaComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el View Composer para alertas
        // Se ejecuta en todas las vistas de navegación para mostrar el badge de alertas no leídas
        View::composer(
            ['layouts.navigation-vertical', 'layouts.app'],
            AlertaComposer::class
        );
    }
}
