<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Deal;
use App\Models\TaskStage;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run()
    {
        $client = Client::query()->firstOrCreate(
            ['name' => 'Aleks Fider',],
            ['name' => 'Aleks Fider',]
        );

        Client::query()->firstOrCreate(
            ['name' => 'Арбыз Хузялябов',],
            ['name' => 'Арбыз Хузялябов',]
        );


        Deal::query()->firstOrcreate(['name' => 'deal 1', 'pipeline_id' => 1,], [
            'name' => 'deal 1',
            'pipeline_id' => 1,
            'responsible_id' => 1,
            'client_id' => 1,
            'stage_id' => 1
        ]);

        Deal::query()->firstOrcreate(['name' => 'deal 2', 'pipeline_id' => 1,], [
            'name' => 'deal 2',
            'pipeline_id' => 1,
            'responsible_id' => 1,
            'client_id' => 2,
            'stage_id' => 1
        ]);

    }
}
