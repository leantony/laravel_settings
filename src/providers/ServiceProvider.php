<?php

namespace Leantony\Settings\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Leantony\Settings\Commands\ManageSettings;
use Leantony\Settings\SettingsHelper;

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
            __DIR__.'/../migrations/' => database_path('/migrations')
        ], 'migrations');

        $this->loadHelpers();
    }

    /**
     * Load helper files
     *
     * @return void
     */
    protected function loadHelpers()
    {
        $files = glob(__DIR__ . '/../helpers/*.php');
        foreach ($files as $file) {
            require_once($file);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('settings', function ($app) {
            return new SettingsHelper($app);
        });

        $this->commands(ManageSettings::class);
    }
}