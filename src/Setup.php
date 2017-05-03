<?php

namespace Leantony\Settings;

use Illuminate\Contracts\Foundation\Application;

trait Setup
{
    /**
     * @var string
     */
    protected static $tableName;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->app['config']['app_settings.categories'];
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        if (self::$tableName === null) {
            self::$tableName = $this->app['config']['app_settings.table_name'];
        }
        return self::$tableName;
    }

    /**
     * @return mixed
     */
    public function getDB()
    {
        return $this->app['db'];
    }

    /**
     * @return mixed
     */
    protected function getCache()
    {
        return $this->app['cache'];
    }

    /**
     * @return bool
     */
    protected function shouldCache()
    {
        return $this->app['config']['app_settings.cache'];
    }
}