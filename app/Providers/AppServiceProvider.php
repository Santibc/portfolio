<?php

namespace App\Providers;

use App\Models\RegistroMercado;
use App\Observers\RegistroMercadoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RegistroMercado::observe(RegistroMercadoObserver::class);
    }
}
