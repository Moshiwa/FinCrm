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
        (new PipelineSeeder())->run();
        (new StagesSeeder())->run();
        (new SettingsSeeder())->run();
        (new UsersSeeder())->run();

        TaskStage::query()->firstOrcreate(['name' => 'Новая'], [
            'name' => 'Новая'
        ]);
        TaskStage::query()->firstOrcreate(['name' => 'Завершена'], [
            'name' => 'Завершена'
        ]);


        (new TestSeeder())->run();
        (new FieldsSeeder())->run();


    }
}
