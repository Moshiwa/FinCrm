<?php

namespace App\Services\Stage;

use App\Models\Setting;
use App\Models\Stage;

class StageService
{
    public function getAllSettings(Stage $stage): array
    {
        $result = [];

        $all_settings = Setting::query()->get()->toArray();
        $stage_settings = $stage->settings->toArray();

        foreach ($all_settings as $index => $setting) {
            $setting['pivot'] = [ 'is_active' => false ];
            $result[$index] = $setting;
            foreach ($stage_settings as $stage_setting) {
                if ($stage_setting['id'] === $setting['id']) {
                    $stage_setting['pivot'] = [ 'is_active' => true ];
                    $result[$index] = $stage_setting;
                }
            }
        }

        return $result;
    }
}
