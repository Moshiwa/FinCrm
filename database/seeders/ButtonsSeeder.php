<?php

namespace Database\Seeders;

use App\Enums\FieldsEnum;
use App\Models\Action;
use App\Models\Button;
use App\Models\Field;
use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class ButtonsSeeder extends Seeder
{
    public function run()
    {
        Button::query()->create([
            'name' => 'testButton',
            'options' => [
                'action' => 'change_stage',
                'display' => [
                    'stages' => [1, 3]
                ],
                'pipeline_id' => 2,
                'stage_id' => 4,
                'responsible_id' => '',
            ],
            'pipeline_id' => 1
        ]);
    }
}
