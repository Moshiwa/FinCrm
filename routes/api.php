<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\PipelineController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => 'space.api'], function () {
        Route::resources([
            'clients' => ClientController::class,
            'users' => UserController::class,
            'deals' => DealController::class,
            'pipelines' => PipelineController::class,
            'fields' => FieldController::class,
        ]);
    });
});
