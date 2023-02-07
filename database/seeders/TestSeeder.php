<?php

namespace Database\Seeders;

use App\Enums\FieldsEnum;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Field;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    public function run()
    {
        $client = Client::query()->firstOrCreate(
            ['name' => 'Aleks Fider',],
            ['name' => 'Aleks Fider',]
        );
        $client->fields()->attach(1, ['value' => '+79875227611']);


        Deal::query()->firstOrcreate(['name' => 'deal 1', 'pipeline_id' => 1,], [
            'name' => 'deal 1',
            'comment' => 'some comment',
            'pipeline_id' => 1,
            'responsible_id' => 1,
            'client_id' => 1,
            'stage_id' => 1
        ]);

        Field::query()->firstOrCreate(['name' => 'Выберите кое что', 'type' => FieldsEnum::select->value], [
            'name' => 'Выберите кое что',
            'type' => FieldsEnum::select->value,
            'options' => [
                'кое', 'что'
            ]
        ]);
    }
}
