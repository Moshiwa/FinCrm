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
       //$client->fields()->sync([1, ['value' => '+79875227611']]);


        Deal::query()->firstOrcreate(['name' => 'deal 1', 'pipeline_id' => 1,], [
            'name' => 'deal 1',
            'pipeline_id' => 1,
            'responsible_id' => 1,
            'client_id' => 1,
            'stage_id' => 1
        ]);

    }
}
