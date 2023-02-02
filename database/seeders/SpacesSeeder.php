<?php

namespace Database\Seeders;

use App\Models\Space;
use Illuminate\Database\Seeder;

class SpacesSeeder extends Seeder
{
    public function run()
    {
        Space::query()->firstOrCreate(['code' => 'main'], [
            'code' => 'main',
            'name' => 'Основное',
            'active' => true,
        ]);
    }
}
