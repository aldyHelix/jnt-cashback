<?php

use Illuminate\Support\Facades\Route;
use Modules\Delivery\Http\Controllers\DeliveryController;

/*
|--------------------------------------------------------------------------
| Delivery Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function() {

    Route::group(['prefix' => 'delivery', 'as' => 'delivery.'], function () {

        // Access module in authentication access

        Route::get('/', [DeliveryController::class, 'index'])->name('index');
        Route::get('/detail/{code}', [DeliveryController::class, 'viewDetail'])->name('detail');
        Route::get('/process/{code}/{id}', [DeliveryController::class, 'process'])->name('process');
        Route::get('/lock/{code}/{id}', [DeliveryController::class, 'lock'])->name('lock');

    });

});

Route::group(['prefix' => 'delivery', 'as' => 'delivery.'], function () {
    Route::get('/', function () {
        return view('delivery::welcome');
    })->name('welcome');
});
