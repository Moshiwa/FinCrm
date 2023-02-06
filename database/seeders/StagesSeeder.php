<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\Space;
use App\Models\Stage;
use App\Models\Status;
use Illuminate\Database\Seeder;

class StagesSeeder extends Seeder
{
    public function run()
    {
        $status_process = Status::query()->select('id')->where('name', 'process')->first();
        $status_done = Status::query()->select('id')->where('name', 'done')->first();
        $status_cancel = Status::query()->select('id')->where('name', 'cancel')->first();
        $pipeline_id = Pipeline::query()->select('id')->first();

        $stages = [
            [
                'name' => 'В работе',
                'status_id' => $status_process->id,
                'pipeline_id' => $pipeline_id->id,
                'default' => true,
                'color' => '#0050FF'
            ],
            [
                'name' => 'Выполнено',
                'status_id' => $status_done->id,
                'pipeline_id' => $pipeline_id->id,
                'color' => '#28FC2A'
            ],
            [
                'name' => 'Отменено',
                'status_id' => $status_cancel->id,
                'pipeline_id' => $pipeline_id->id,
                'color' => '#FE3F6D'
            ],

        ];

        foreach ($stages as $stage) {

            Stage::query()->firstOrCreate([
                'pipeline_id' => $stage['pipeline_id'],
                'status_id' => $stage['status_id'],
                'name' => $stage['name'],
            ], $stage);
        }
    }
}
