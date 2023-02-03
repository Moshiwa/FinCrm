<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run()
    {
        Pipeline::query()->firstOrCreate(['name' => 'Основная'], [
            'name' => 'Основная',
        ]);
    }
}
