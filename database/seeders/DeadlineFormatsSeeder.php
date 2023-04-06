<?php

namespace Database\Seeders;

use App\Models\DeadlineFormat;
use Illuminate\Database\Seeder;

class DeadlineFormatsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'minute',
                'value' => 60
            ],
            [
                'name' => 'hour',
                'value' => 3600
            ],
            [
                'name' => 'day',
                'value' => 86400
            ],
        ];

        foreach ($data as $datum) {
            DeadlineFormat::query()->updateOrCreate(['name' => $datum['name']], $datum);
        }
    }
}
