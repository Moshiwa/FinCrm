<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        User::query()->firstOrCreate([ 'email' => 'admin@mail.ru' ], [
            'name' => 'admin',
            'email' => 'admin@mail.ru',
            'password' => bcrypt('itpolice')
        ]);
    }
}
