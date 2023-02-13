<?php

namespace Database\Seeders;

use App\Enums\FieldsEnum;
use App\Models\Field;
use App\Models\Pipeline;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'name' => 'display-in-deal',
                'type' => 'field',
                'description' => 'Отображать у сделок'
            ],
            [
                'name' => 'display-in-client',
                'type' => 'field',
                'description' => 'Отображать у клиентов'
            ],
            [
                'name' => 'comment',
                'type' => 'button',
                'description' => 'Комментировать'
            ],
            [
                'name' => 'file-upload',
                'type' => 'button',
                'description' => 'Прикрепить файл'
            ],

        ];

        foreach ($settings as $setting) {
            Setting::query()->firstOrCreate($setting);
        }
    }
}
