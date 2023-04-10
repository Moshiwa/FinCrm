<?php

namespace Database\Seeders;

use App\Models\DeadlineFormat;
use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class StagesSeeder extends Seeder
{
    public function run()
    {
        $pipeline_id = Pipeline::query()->select('id')->first();
        $format = DeadlineFormat::query()->where('name', 'day')->first();
        $stages = [
            [
                'name' => 'В работе',
                'pipeline_id' => $pipeline_id->id,
                'deadline' => 1,
                'deadline_format_id' => $format->id,
                'lft' => 0
            ],
            [
                'name' => 'Выполнено',
                'pipeline_id' => $pipeline_id->id,
                'deadline' => 1,
                'deadline_format_id' => $format->id,
                'lft' => 0
            ],
            [
                'name' => 'Отменено',
                'pipeline_id' => $pipeline_id->id,
                'deadline' => 1,
                'deadline_format_id' => $format->id,
                'lft' => 0
            ],

        ];

        foreach ($stages as $stage) {

            Stage::query()->firstOrCreate([
                'pipeline_id' => $stage['pipeline_id'],
                'name' => $stage['name'],
                'deadline' => 4,
                'deadline_format_id' => 1
            ], $stage);
        }
    }
}
