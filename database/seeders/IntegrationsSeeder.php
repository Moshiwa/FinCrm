<?php

namespace Database\Seeders;

use App\Models\Integration;
use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class IntegrationsSeeder extends Seeder
{
    public function run()
    {
        Integration::query()->firstOrCreate(['name' => 'sms_center'], [
            'name' => 'sms_center',
            'title' => 'СмсЦентр',
            'data' => [
                'theme' => '',
                'sender' => ''
            ]
        ]);
    }
}
