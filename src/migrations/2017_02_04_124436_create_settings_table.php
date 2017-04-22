<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getConfig('table_name'), function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('category');
            $table->string('description');
            $table->unsignedTinyInteger('multivalued')->default(0);
            $table->text('value')->nullable();
        });
    }

    /**
     * Get config param
     *
     * @param $value
     * @return mixed
     */
    protected function getConfig($value)
    {
        return config('app_settings.' . $value);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->getConfig('table_name'));
    }
}