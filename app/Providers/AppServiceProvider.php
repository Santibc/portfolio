<?php

namespace App\Providers;

use App\Models\TipoPago;
use Illuminate\Support\Facades\Schema;
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
        // Compartir tipos de pago activos y mapa de badges con vistas que los necesitan.
        // Wrap en try/catch para no romper migrate fresh cuando la tabla aun no existe.
        View::composer(['ordenes.*', 'contabilidad.*'], function ($view) {
            try {
                if (Schema::hasTable('tipos_pago')) {
                    $view->with('tiposPago', TipoPago::opciones());
                    $view->with('tiposPagoMapa', TipoPago::mapaBadges());
                }
            } catch (\Throwable $e) {
                // ignorar en contextos donde la BD no esta disponible
            }
        });
    }
}
