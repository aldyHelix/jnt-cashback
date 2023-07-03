<?php

use Illuminate\Support\Facades\Route;
use Modules\CashbackPickup\Http\Controllers\CashbackPickupController;

/*
|--------------------------------------------------------------------------
| CashbackPickup Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function() {

    Route::group(['prefix' => 'cashbackpickup', 'as' => 'cashbackpickup.'], function () {

        // Access module in authentication access
        Route::get('/{grade}', [CashbackPickupController::class, 'index'])->name('index');
        Route::get('/detail/{code}/{grade}', [CashbackPickupController::class, 'viewDetail'])->name('detail');
        Route::get('/process/{code}/{grade}/{id}', [CashbackPickupController::class, 'process'])->name('process');
        Route::get('/lock/{code}/{grade}/{id}', [CashbackPickupController::class, 'lock'])->name('lock');
    });

});

Route::group(['prefix' => 'cashbackpickup', 'as' => 'cashbackpickup.'], function () {
    Route::get('/', function () {
        return view('cashbackpickup::welcome');
    })->name('welcome');
});
