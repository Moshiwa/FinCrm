<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Deal;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        (new UsersSeeder())->run();
        (new SpacesSeeder())->run();
        (new StatusesSeeder())->run();
        (new PermissionSeeder())->run();
        (new PipelineSeeder())->run();
        (new StagesSeeder())->run();


        Client::query()->create([
            'name' => 'Test Client',
            'phone' => '8 800 555 35 35'
        ]);
        Deal::query()->create([
            'name' => 'test Deal',
            'comment' => 'comment test Deal',
            'pipeline_id' => 1,
            'responsible_id' => 1,
            'stage_id' => 1,
            'client_id' => 1
        ]);
    }
}
