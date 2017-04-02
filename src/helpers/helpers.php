<?php
use Illuminate\Support\Facades\App;

if (!function_exists('settings')) {
    /**
     * Fetch settings
     *
     * @param $key
     * @param null $default
     * @return \Leantony\Settings\SettingsHelper|mixed
     */
    function settings($key = null, $default = null)
    {
        if ($key === null) {
            return App::make('settings');
        }
        return App::make('settings')->get($key, $default);
    }
}
