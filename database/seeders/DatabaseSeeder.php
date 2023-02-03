<?php

namespace Database\Seeders;

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
    }
}
