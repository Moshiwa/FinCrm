<?php

namespace Database\Seeders;

use App\Enums\FieldsEnum;
use App\Models\Field;
use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class FieldsSeeder extends Seeder
{
    public function run()
    {
        Field::query()->firstOrCreate(
            ['name' => 'Номер телефона', 'type' => FieldsEnum::phone],
            ['name' => 'Номер телефона', 'type' => FieldsEnum::phone, 'entity' => 'client']);

        Field::query()->firstOrCreate(
            ['name' => 'Email', 'type' => FieldsEnum::email],
            ['name' => 'Email', 'type' => FieldsEnum::email, 'entity' => 'client']);

        Field::query()->firstOrCreate(
            ['name' => 'Email', 'type' => FieldsEnum::email, 'entity' => 'deal'],
            ['name' => 'Email', 'type' => FieldsEnum::email, 'entity' => 'deal']);

        Field::query()->firstOrCreate(
            ['name' => 'Активность', 'type' => FieldsEnum::select, 'entity' => 'client'],
            [
                'name' => 'Активность',
                'type' => FieldsEnum::select,
                'entity' => 'client',
                'options' => '[{"value":"Да"},{"value":"Нет"}]'
            ]);
    }
}
