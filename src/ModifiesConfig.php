<?php

namespace Leantony\Settings;

use Illuminate\Support\Facades\Artisan;

trait ModifiesConfig
{
    use Setup;

    /**
     * Grab settings we need from laravel config
     *
     * @return array
     */
    public function grab()
    {
        $config_initial = $this->app['config'];

        $categories = $this->getCategories();

        $grabs = [];
        // loop over specified categories, extracting what we need and
        // ignoring what we don't need
        foreach ($categories as $key => $value) {
            if (array_has($config_initial, $key)) {
                $config = $config_initial[$key];
                $ignore = is_array($value) ? array_get($value, 'ignore') : $value;
                if ($ignore) {
                    if ($ignore == '*') {
                        $config = null;
                    } else {
                        if (is_array($ignore)) {
                            foreach ($ignore as $item) {
                                if (array_has($config, $item)) {
                                    array_pull($config, $item);
                                }
                            }
                        } else {
                            if (array_has($config, $ignore)) {
                                array_pull($config, $ignore);
                            }
                        }
                    }
                }
                $grabs[$key] = $config;
            }
        }
        return $grabs;
    }

    /**
     * Insert settings from config files into the database
     *
     * @param $values
     */
    public function insert(array $values)
    {
        $settings = [];
        foreach ($values as $key => $value) {
            $category = $key;
            if (is_array($value)) {
                $n = $category;
                foreach ($value as $item => $name) {
                    $n .= '.' . $item;
                    $specific_value = $name;
                    // default description
                    $description = 'Specify description for ' . $n;
                    $settings[] = [
                        'key' => trim($n),
                        'category' => $category,
                        'multivalued' => count(json_decode($specific_value, JSON_OBJECT_AS_ARRAY)) > 1,
                        'value' => serialize($specific_value),
                        'description' => $description
                    ];
                    // reset to category name
                    $n = $category;
                }
            } else {
                continue;
            }
        }
        $this->getDB()->table($this->getTableName())->insert($settings);
    }

    /**
     * Replace specified config values with ones in the DB
     * This should be called in the boot method of a service provider
     *
     * @param null $category
     * @return void
     */
    public function replaceLoaded($category = null)
    {
        if ($category) {
            $values = $this->getDB()
                ->table($this->getTableName())
                ->where(['category' => $category])
                ->pluck('value', 'key');
            if ($values !== null) {
                $this->doCache($values);
            }
        } else {
            foreach ($this->getCategories() as $key => $value) {
                $values = $this->getDB()
                    ->table($this->getTableName())
                    ->where(['category' => $key])
                    ->pluck('value', 'key');
                // get all values from the collection
                if ($values !== null) {
                    $this->doCache($values);
                }
            }
        }
    }

    /**
     * @param null $values
     */
    public function doCache($values)
    {
        if ($this->shouldCache()) {
            Artisan::call('config:clear');
            $this->app['config']->set($values);
            Artisan::call('config:cache');
        }
    }
}