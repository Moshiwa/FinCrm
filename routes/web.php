<?php

use App\Http\Controllers\DealController;
use Illuminate\Support\Facades\Log;
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
    return redirect('/admin');
})->middleware(array_merge(
    (array) config('backpack.base.web_middleware', 'web'),
    (array) config('backpack.base.middleware_key', 'admin')
));

Route::get('/webhook', function (\Illuminate\Http\Request $request) {
    $link = $request->get('file_link');
    Log::info('WEBHOOK:'. json_encode($link));
});
