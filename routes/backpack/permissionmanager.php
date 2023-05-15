<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => 'Backpack\PermissionManager\app\Http\Controllers',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
//    'middleware' => ['web', backpack_middleware(), "can:permission"],
    'middleware' => ['web', backpack_middleware()],
], function () {
    Route::crud('permission', 'PermissionCrudController');
//    Route::crud('role', 'RoleCrudController');
//    Route::crud('user', 'UserCrudController');
});
Route::group([
    'namespace'  => 'App\Http\Controllers\Admin\PermissionManager',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {
    Route::crud('role', 'RoleCrudController');
    Route::crud('user', 'UserCrudController');
});
