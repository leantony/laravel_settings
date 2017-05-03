<?php

namespace Leantony\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'key';
    protected $fillable = ['key', 'value', 'category', 'description'];

    public static function boot()
    {
        parent::boot();

        static::observe(SettingObserver::class);
    }

    public function getTable()
    {
        return app('config')->getTableName();
    }

    /**
     * Set the setting value
     *
     * @param  string $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }

    /**
     * @param $attribute
     * @return string
     */
    public function getKeyAttribute($attribute)
    {
        return $attribute;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getValueAttribute($attribute)
    {
        return $this->unserialize($attribute);
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
}