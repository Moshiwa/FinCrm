<?php

namespace Database\Seeders;

use App\Models\FieldType;
use Illuminate\Database\Seeder;

class FieldTypesSeeder extends Seeder
{
    public function run()
    {
        $types = [
            'phone', 'email', 'number',
            'select', 'checkbox', 'date', 'address'
        ];

        foreach ($types as $type) {
            FieldType::query()->updateOrCreate(
                ['name' => $type],
                ['name' => $type]
            );
        }
    }
}
