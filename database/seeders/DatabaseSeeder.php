<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        (new DeadlineFormatsSeeder())->run();
        (new IntegrationsSeeder())->run();
        (new FieldTypesSeeder())->run();
        (new SpacesSeeder())->run();
        (new PermissionSeeder())->run();
        (new UsersSeeder())->run();
    }
}
