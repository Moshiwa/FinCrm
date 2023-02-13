<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class StagesSeeder extends Seeder
{
    public function run()
    {
        $pipeline_id = Pipeline::query()->select('id')->first();

        $stages = [
            [
                'name' => 'В работе',
                'pipeline_id' => $pipeline_id->id,
                'color' => '#0050FF'
            ],
            [
                'name' => 'Выполнено',
                'pipeline_id' => $pipeline_id->id,
                'color' => '#28FC2A'
            ],
            [
                'name' => 'Отменено',
                'pipeline_id' => $pipeline_id->id,
                'color' => '#FE3F6D'
            ],

        ];

        foreach ($stages as $stage) {

            Stage::query()->firstOrCreate([
                'pipeline_id' => $stage['pipeline_id'],
                'name' => $stage['name'],
            ], $stage);
        }
    }
}
