<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        (new FieldTypesSeeder())->run();
        (new UsersSeeder())->run();
        (new SpacesSeeder())->run();
        (new PermissionSeeder())->run();
        (new PipelineSeeder())->run();
        (new StagesSeeder())->run();
        (new SettingsSeeder())->run();

        (new TestSeeder())->run();
        (new FieldsSeeder())->run();


    }
}
