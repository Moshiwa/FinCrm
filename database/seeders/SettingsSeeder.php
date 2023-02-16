<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Setting::query()->firstOrCreate(['key' => 'comment', 'field' => 'button'], [
            'key' => 'comment',
            'name' => 'Комментировать',
            'description' => 'Возможность комментировать',
            'field' => 'button',
            'active' => 1
        ]);

        Setting::query()->firstOrCreate(['key' => 'document', 'field' => 'button'], [
            'key' => 'document',
            'name' => 'Прикрепить документы',
            'description' => 'Возможность прикреплять документы',
            'field' => 'button',
            'active' => 1
        ]);
    }
}
