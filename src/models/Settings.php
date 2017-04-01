<?php

namespace Leantony\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'key';
    protected $fillable = ['key', 'value', 'category', 'description'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = settings()->getTableName();
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
        return settings()->unserialize($attribute);
    }
}