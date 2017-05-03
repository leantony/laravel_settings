<?php

namespace Leantony\Settings;

use Illuminate\Contracts\Foundation\Application;

class SettingsHelper
{
    use ModifiesConfig;

    /**
     * SettingsCache constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->app['config']->get($name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->app['config']->all();
    }
}