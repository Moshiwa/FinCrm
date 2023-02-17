<?php

namespace App\Services\Stage;

use App\Enums\SettingKeysEnum;
use App\Models\Setting;
use App\Models\Stage;

class StageService
{
    public function getAllSettings(Stage $stage): array
    {
        $result = [];

        $all_settings = Setting::query()->get()->toArray();
        $all_settings = $this->stageChangeFillOptionsSettings($stage, $all_settings);

        $stage_settings = $stage->settings->toArray();

        foreach ($all_settings as $index => $setting) {
            $result[$index] = $setting;
            foreach ($stage_settings as $stage_setting) {
                if ($stage_setting['id'] === $setting['id']) {
                    if (isset($stage_setting['field']['multiple'])) {
                        $setting['field']['value'][] = $stage_setting['pivot']['value'];
                    } else {
                        $setting['field']['value'] = (bool)$stage_setting['pivot']['value'] ?? false;
                    }

                    $result[$index] = $setting;
                }
            }
        }

        return $result;
    }

    private function stageChangeFillOptionsSettings($stage, $settings): array
    {
        $result = [];

        foreach ($settings as $index => $setting) {
            $result[$index] = $setting;
            if ($setting['key'] === SettingKeysEnum::change_stage->value) {
                $stages = Stage::query()
                    ->where('pipeline_id', $stage->pipeline_id)
                    ->whereNot('id', $stage->id)
                    ->get();

                foreach ($stages as $stage) {
                    $setting['field']['options'][$stage->id] = $stage->name;
                }

                $result[$index] = $setting;
            }
        }

        return $result;
    }

    public static function getDefultStages()
    {
        return [
            [
                'name' => 'В работе',
                'color' => '#0050FF'
            ],
            [
                'name' => 'Выполнено',
                'color' => '#28FC2A'
            ],
            [
                'name' => 'Отменено',
                'color' => '#FE3F6D'
            ],

        ];
    }
}
