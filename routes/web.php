<?php

use App\Http\Controllers\Admin\TelephonyController;
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
Route::middleware(array_merge(
    (array)config('backpack.base.web_middleware', 'web'),
    (array)config('backpack.base.middleware_key', 'admin')
))->group(function () {
    Route::get('/', function () {
        return redirect('/admin/deal');
    });
    Route::get('/admin', function () {
        return redirect('/admin/deal');
    });
});

Route::middleware('client.ip')->group(function () {
    Route::get('/webhook/record', [TelephonyController::class, 'recordFromWebhook']);
});
