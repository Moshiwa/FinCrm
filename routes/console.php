<?php

use App\Models\User;
use App\Services\Space\SpaceService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('fix', function () {
    $user = User::query()->where('email', 'admin@mail.ru')->first();
    $user->password = bcrypt('itpolice');
    $user->roles()->sync(1);
});

Artisan::command('create:superdamin {email}', function ($email) {
    $password = Str::random(11);
    $user = User::query()->where('email', $email)->first();
    if (!$user) {
        $user = new User();
        $user->name = $email;
        $user->email = $email;
    }
    $user->password = Hash::make($password);
    $user->save();

    $user->roles()->sync([1]);

    dump($password);
});

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('api-token {userId}', function ($userId) {

    $user = User::findOrFail($userId);
    $token = $user->createToken('api');

    dump(['token' => $token->plainTextToken]);
});
