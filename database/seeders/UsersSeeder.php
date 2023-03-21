<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $user = User::query()->firstOrCreate([ 'email' => 'admin@mail.ru' ], [
            'name' => 'admin',
            'email' => 'admin@mail.ru',
            'password' => bcrypt('itpolice')
        ]);
        $user->roles()->sync(1);

        User::query()->firstOrCreate([ 'email' => 'test@mail.ru' ], [
            'name' => 'Олег Петрович',
            'email' => 'test@mail.ru',
            'password' => bcrypt('test')
        ]);

        User::query()->firstOrCreate([ 'email' => 'test1@mail.ru' ], [
            'name' => 'Сифон Паршев',
            'email' => 'test1@mail.ru',
            'password' => bcrypt('test1')
        ]);
    }
}
