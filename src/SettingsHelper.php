<?php

namespace Leantony\Settings;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Leantony\Settings\Models\SettingObserver;
use Leantony\Settings\Models\Settings;

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
     * Forget a single tagged key
     *
     * @param $category
     */
    public function forgetOne($category)
    {
        $this->getCache()->tags(['settings', $category])->flush();
    }

    /**
     * Handle settings events
     */
    public static function observe()
    {
        Settings::observe(SettingObserver::class);
    }

    /**
     * Refresh single setting
     *
     * @param $category
     */
    public function refreshOne($category)
    {
        $values = $this->getDB()->table($this->getTableName())->where(['category' => $category])->pluck('value', 'key');
        $this->getCache()->tags(['settings', $category])->put($this->getCacheKey($category), $values,
            $this->getCacheDuration());
    }

    /**
     * Get the key to be used in the cache
     *
     * @param $category
     * @return string
     */
    protected function getCacheKey($category)
    {
        return $this->getRootCacheKey() . $category;
    }

    /**
     * Refresh the cache
     */
    public function refresh()
    {
        $this->forget();

        $this->cacheAll();
    }

    /**
     * Forget all cache values
     *
     * @return void
     */
    public function forget()
    {
        foreach ($this->getCategories() as $key => $value) {
            $this->getCache()->tags(['settings', $key])->flush();
        }
        $this->getCache()->forget($this->getRootCacheKey());
    }

    /**
     * Cache all settings
     *
     * @return void
     */
    public function cacheAll()
    {
        if (!$this->isCached()) {
            foreach ($this->getCategories() as $key => $value) {
                $values = $this->getDB()->table($this->getTableName())->where(['category' => $key])->pluck('value',
                    'key');
                $this->getCache()->tags(['settings', $key])->put($this->getCacheKey($key), $values,
                    $this->getCacheDuration());
            }
            $this->getCache()->put($this->getRootCacheKey(), ['loaded' => true, 'date' => time()],
                $this->getCacheDuration());
        }
    }

    /**
     * Check if settings have been cached
     *
     * @return boolean
     */
    protected function isCached()
    {
        return $this->getCache()->has($this->getRootCacheKey());
    }

    /**
     * Format a key name to a friendly value
     *
     * @param $category
     * @param $name
     * @return string
     */
    public function formatKeyName($category, $name)
    {
        return ucfirst(str_replace($category . '.', '', str_replace('_', ' ', $name)));
    }

    /**
     * Format an already formatted key to the original value
     *
     * @param $formatted
     * @param $category
     * @return null|string
     */
    public function formatKeyToDefault($formatted, $category)
    {
        // strip out the first instance of the category name
        $value = str_replace($category . '_', '', $formatted);
        // append the category and a dot to the value above
        return $category . '.' . str_replace(' ', '_', $value);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->__call($name, []);
    }

    /**
     * Handle dynamic calls into the class
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // adopted from https://secure.php.net/manual/en/function.str-split.php#81836
        $data = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $method);

        $values = explode(' ', trim($data));

        $category = strtolower($values[0]);

        // remove the category from whats to be appended.
        unset($values[0]);

        // dynamically append the underscore based on the values imploded
        $key = null;
        foreach ($values as $value) {
            $key .= strtolower($value) . '_';
        }

        // remove last underscore
        $key = Str::replaceLast('_', '', $key);

        return $this->get($category . '.' . $key, $parameters);
    }

    /**
     * Get a setting value from the cache
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (str_contains($key, '.')) {
            $category = substr($key, 0, strpos($key, '.'));
        } else {
            $category = $key;
            return $this->getByCategory($category);
        }

        $settings = $this->getByCategory($category);
        if ($settings === null) {
            return null;
        }
        if (is_array($default) && empty($default)) {
            $default = null;
        }
        return $settings->get($key, $default);
    }

    /**
     * Fetch all settings associated with a category
     *
     * @param $category
     * @return Collection|null
     * @throws \Exception
     */
    public function getByCategory($category)
    {
        if (!$this->isCached()) {
            $this->cacheAll();
        }
        $context = $this;
        /** @var Collection $values */
        $values = $this->getCache()->tags(['settings', $category])->get($this->getCacheKey($category));
        if ($values !== null) {
            $data = $values->map(function ($value) use ($context) {
                return $context->unserialize($value);
            });

            return $data;
        }
        $this->app['log']->error(sprintf('Settings with category %s not found', $category));
        return null;
    }

    /**
     * Unserialize an attribute value
     *
     * @param $str
     * @return mixed|null|string
     */
    public function unserialize($str)
    {
        try {
            $value = unserialize($str);
        } catch (\Exception $e) {
            $this->app['log']->error($e->getMessage());
            return null;
        }

        if (is_array($value)) {
            return json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getByCategory('app')->toJson();
    }
}