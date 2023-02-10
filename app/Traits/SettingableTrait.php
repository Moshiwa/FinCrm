<?php

namespace App\Traits;

use App\Models\Setting;

trait SettingableTrait
{
    public function settings()
    {
        return static::morphToMany(Setting::class, 'settingable')->withPivot('is_enable');
    }
}
