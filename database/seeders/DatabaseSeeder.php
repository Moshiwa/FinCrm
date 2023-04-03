<?php

namespace Database\Seeders;

use App\Models\TaskStage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        (new IntegrationsSeeder())->run();
        (new FieldTypesSeeder())->run();
        (new SpacesSeeder())->run();
        (new PermissionSeeder())->run();
        (new UsersSeeder())->run();
    }
}
