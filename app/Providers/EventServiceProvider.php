<?php

namespace App\Providers;

use App\Events\CotizacionCreada;
use App\Listeners\CrearCuentaCliente;
use App\Models\SolicitudCotizacion;
use App\Observers\SolicitudCotizacionObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CotizacionCreada::class => [
            CrearCuentaCliente::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Registrar Observer para notificaciones de cambio de estado
        SolicitudCotizacion::observe(SolicitudCotizacionObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
