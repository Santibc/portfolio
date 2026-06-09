<?php

namespace App\Providers;

use App\Models\SiigoConfig;
use App\Services\Siigo\SiigoClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // SiigoClient depende de la fila de configuración real (credenciales + IDs).
        // Sin este binding, el contenedor inyectaría un SiigoConfig vacío y la
        // autenticación fallaría. Se resuelve con la config persistida actual.
        $this->app->bind(SiigoClient::class, fn () => new SiigoClient(SiigoConfig::current()));
    }

    public function boot(): void
    {
        //
    }
}
