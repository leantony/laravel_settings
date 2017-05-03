<?php

namespace Leantony\Settings\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Leantony\Settings\Commands\ManageSettings;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/app_settings.php' => config_path('app_settings.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('/migrations')
        ], 'migrations');

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(ManageSettings::class);
    }
}