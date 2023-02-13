<?php

use App\Http\Controllers\DealController;
use App\Http\Controllers\PipelineController;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('deal')->name('deal.')->group(function () {
    Route::post('/update', [DealController::class, 'update'])->name('update');
    Route::get('/get_stages/{pipeline}', [DealController::class, 'getStagesByPipeline'])->name('stages');
    Route::post('/{deal}/save_files', [DealController::class, 'saveFiles'])->name('saveFiles');
    Route::post('/{deal}/change_pipeline', [DealController::class, 'changePipeline'])->name('changePipeline');
});

Route::prefix('pipeline')->name('pipeline.')->group(function () {
    Route::post('/', [PipelineController::class, 'create'])->name('create');
    Route::get('/{pipeline}', [PipelineController::class, 'get'])->name('get');
    Route::delete('/{pipeline}', [PipelineController::class, 'delete'])->name('delete');
    Route::post('/update', [PipelineController::class, 'update'])->name('update');
});


//ToDo удалить
Route::prefix('settings')->name('settings.')->group(function () {
    Route::prefix('pipeline')->name('pipeline.')->group(function () {
        Route::post('/', [PipelineController::class, 'create'])->name('create');
        Route::get('/{pipeline}', [PipelineController::class, 'get'])->name('get');
        Route::delete('/{pipeline}', [PipelineController::class, 'delete'])->name('delete');
    });

    Route::post('/save', function (Request $request) {
        $pivot = $request->get('pivot');
        $stage_id = $pivot['settingable_id'];
        $setting_id = $pivot['setting_id'];
        $is_enable = $pivot['is_enable'];

        $stage = Stage::query()->find($stage_id);
        $stage->settings()->attach([$setting_id => ['is_enable' => $is_enable]]);
    });
});
