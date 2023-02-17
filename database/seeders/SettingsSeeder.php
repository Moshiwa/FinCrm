<?php

namespace Database\Seeders;

use App\Enums\SettingKeysEnum;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Setting::query()->firstOrCreate(['key' => SettingKeysEnum::leave_comment->value], [
            'key' => SettingKeysEnum::leave_comment->value,
            'name' => 'Комментировать',
            'description' => 'Возможность комментировать',
            'field' => [
                'name' => 'stage_comment',
                'label' => 'Комментировать',
                'type' => 'button'
            ],
            'active' => 1
        ]);

        Setting::query()->firstOrCreate(['key' => SettingKeysEnum::upload_document->value], [
            'key' => SettingKeysEnum::upload_document->value,
            'name' => 'Прикрепить документы',
            'description' => 'Возможность прикреплять документы',
            'field' => [
                'name' => 'stage_upload',
                'label' => 'Прикрепить документы',
                'type' => 'button'
            ],
            'active' => 1
        ]);

        Setting::query()->firstOrCreate(['key' => SettingKeysEnum::change_stage->value], [
            'key' => SettingKeysEnum::change_stage->value,
            'name' => 'Запретить менять этапы',
            'description' => 'Выбрать этапы для запрета',
            'field' => [
                'name' => 'change_stage',
                'label' => 'Запрещенные стадии',
                'type' => 'select',
                'multiple' => true,
                'options' => []
            ],
            'active' => 1
        ]);

    }
}
