<?php

namespace App\Providers;
use App\Services\Contracts\ApiClientFactoryInterface;
use App\Services\Calendly\CalendlyClientFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ApiClientFactoryInterface::class, CalendlyClientFactory::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
