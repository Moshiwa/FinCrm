<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SpaceCrudController;
// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('user', 'UserCrudController');
    Route::crud('fields', 'FieldCrudController');
    Route::crud('pipelines', 'PipelineCrudController');
    Route::crud('space', 'SpaceCrudController');
    Route::crud('deal', 'DealCrudController');


//ToDo Завернуть в permission middleware
    Route::get('space-change/{code}', [SpaceCrudController::class, 'spaceChange'])->name('space.change');
    Route::crud('settings', 'SettingsCrudController');
});