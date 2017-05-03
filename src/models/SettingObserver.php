<?php

namespace Leantony\Settings\Models;

use Leantony\Settings\SettingsHelper;

class SettingObserver
{
    /**
     * Listen to the Settings updated event.
     *
     * @param  Settings $setting
     * @return void
     */
    public function updated(Settings $setting)
    {
        $instance = app('settings');
        /** @var $instance SettingsHelper */

        // clear the settings cache
        $instance->replaceLoaded($setting->category);
    }
}