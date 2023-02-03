<?php

use App\Services\Space\SpaceService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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

/*Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('migrate2 {space=0}', function ($space) {
    if($space) {
        SpaceService::migrate($space);
    } else {
        SpaceService::migrateAll();
    }
});
Artisan::command('migrate2:rollback {space=0} {--step=1}', function ($space, $step) {
    if($space) {
        SpaceService::migrateRollback($space, $step);
    } else {
        SpaceService::migrateRollbackAll($step);
    }
});
Artisan::command('migrate2:reset {space}', function ($space) {
    SpaceService::migrateReset($space);
});
Artisan::command('migrate2:fresh {space}', function ($space) {
    SpaceService::migrateFresh($space);
});*/
