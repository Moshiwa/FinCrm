<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusesSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            'process',
            'done',
            'cancel'
        ];

        foreach ($statuses as $status) {
            Status::query()->firstOrCreate(
                [ 'name' => $status ],
                [ 'name' => $status ]
            );
        }
    }
}
