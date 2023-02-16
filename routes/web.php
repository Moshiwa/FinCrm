<?php

use App\Http\Controllers\DealController;
use App\Http\Controllers\FieldController;
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
    return redirect('admin');
});

Route::prefix('deal')->name('deal.')->group(function () {
    Route::post('/update', [DealController::class, 'update'])->name('update');
    Route::get('/get_stages/{pipeline}', [DealController::class, 'getStagesByPipeline'])->name('stages');
    Route::post('/{deal}/save_files', [DealController::class, 'saveFiles'])->name('saveFiles');
    Route::post('/{deal}/change_pipeline', [DealController::class, 'changePipeline'])->name('changePipeline');
    Route::get('/{deal}/load_comments', [DealController::class, 'loadComments'])->name('loadComments');
});

Route::prefix('pipeline')->name('pipeline.')->group(function () {
    Route::post('/', [PipelineController::class, 'create'])->name('create');
    Route::get('/{pipeline}', [PipelineController::class, 'get'])->name('get');
    Route::delete('/{pipeline}', [PipelineController::class, 'delete'])->name('delete');
    Route::post('/update', [PipelineController::class, 'update'])->name('update');
});

Route::prefix('field')->name('field.')->group(function () {
    Route::post('/save', [FieldController::class, 'save'])->name('.save');
});
