<?php

use App\Http\Controllers\Admin\DealButtonCrudController;
use App\Http\Controllers\Admin\ClientCrudController;
use App\Http\Controllers\Admin\DealCrudController;
use App\Http\Controllers\Admin\FieldCrudController;
use App\Http\Controllers\Admin\ManagerCrudController;
use App\Http\Controllers\Admin\ReportCrudController;
use App\Http\Controllers\Admin\SenderController;
use App\Http\Controllers\Admin\StageCrudController;
use App\Http\Controllers\Admin\TaskButtonCrudController;
use App\Http\Controllers\Admin\TaskCrudController;
use App\Http\Controllers\Admin\UserCrudController;
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
    Route::crud('field', 'FieldCrudController');
    Route::crud('pipeline', 'PipelineCrudController');
    Route::crud('space', 'SpaceCrudController');
    Route::crud('deal', 'DealCrudController');
    Route::crud('client', 'ClientCrudController');
    Route::crud('stage', 'StageCrudController');
    Route::crud('button', 'DealButtonCrudController');
    Route::crud('task', 'TaskCrudController');
    Route::crud('task-stage', 'TaskStageCrudController');
    Route::crud('task-button', 'TaskButtonCrudController');
    Route::crud('manager', 'ManagerCrudController');
    Route::crud('report', 'ReportCrudController');

    Route::get('space-change/{code}', [SpaceCrudController::class, 'spaceChange'])->name('space.change');

    Route::prefix('deal')->name('deal.')->group(function () {
        Route::delete('/{deal}', [DealCrudController::class, 'delete'])->name('delete');
        Route::post('/update', [DealCrudController::class, 'update'])->name('update');
        Route::get('/get_stages/{pipeline}', [DealCrudController::class, 'getStagesByPipeline'])->name('stages');
        Route::post('/{deal}/change_pipeline', [DealCrudController::class, 'changePipeline'])->name('changePipeline');
        Route::get('/{deal}/load_comments', [DealCrudController::class, 'loadComments'])->name('loadComments');
        Route::get('/client/{id}', [DealCrudController::class, 'dealCreate'])->name('deal.deal-create');
        Route::post('/button/save', [DealButtonCrudController::class, 'save'])->name('save');
        Route::delete('/button/{button}', [DealButtonCrudController::class, 'delete'])->name('delete');
    });

    Route::prefix('task')->name('task.')->group(function () {
        Route::delete('/{task}', [TaskCrudController::class, 'delete'])->name('delete');
        Route::post('/update', [TaskCrudController::class, 'update'])->name('update');
        Route::get('/{task}/load_comments', [TaskCrudController::class, 'loadComments'])->name('loadComments');
        Route::get('/new', [TaskCrudController::class, 'taskCreate'])->name('task-create');
        Route::post('/button/save', [TaskButtonCrudController::class, 'save'])->name('button.save');
        Route::delete('/button/{button}', [TaskButtonCrudController::class, 'delete'])->name('button.delete');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('find-users', [UserCrudController::class, 'findUsers'])->name('find');
        Route::get('/{user}/load_comments', [ManagerCrudController::class, 'loadComments'])->name('loadComments');
    });

    Route::prefix('stage')->name('stage.')->group(function () {
        Route::post('/update', [StageCrudController::class, 'update'])->name('update');
    });

    Route::prefix('field')->name('field.')->group(function () {
        Route::get('/{field}/toggle-activity', [FieldCrudController::class, 'toggleActivity'])->name('toggleActivity');
        Route::get('/find-address', [FieldCrudController::class, 'findAddress'])->name('findAddress');
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/generate', [ReportCrudController::class, 'reportGenerate'])->name('generate');
    });

    Route::prefix('sender')->name('sender.')->group(function () {
        Route::post('/send', [SenderController::class, 'send'])->name('send');
    });
    Route::crud('integration', 'IntegrationCrudController');
});
