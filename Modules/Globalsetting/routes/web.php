<?php

use Illuminate\Support\Facades\Route;
use Modules\Globalsetting\Http\Controllers\GeneralSettingController;
use Modules\Globalsetting\Http\Controllers\DeliverySettingController;
use Modules\Globalsetting\Http\Controllers\RekapSettingController;
use Modules\Globalsetting\Http\Controllers\SumberWaybillSettingController;

/*
|--------------------------------------------------------------------------
| Globalsetting Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'globalsetting', 'as' => 'globalsetting.'], function () {

        // Access module in authentication access

        Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {

            // Access module in authentication access
            Route::get('/', [GeneralSettingController::class, 'index'])->name('index');
            Route::get('/create', [GeneralSettingController::class, 'create'])->name('create');
            Route::post('/store', [GeneralSettingController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [GeneralSettingController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [GeneralSettingController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [GeneralSettingController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'sumber-waybill', 'as' => 'sumber-waybill.'], function () {

            // Access module in authentication access
            Route::get('/', [SumberWaybillSettingController::class, 'index'])->name('index');
            Route::post('/sync', [SumberWaybillSettingController::class, 'syncSumberWaybill'])->name('sync');
            // Route::get('/create', [GeneralSettingController::class, 'create'])->name('create');
            // Route::post('/store', [GeneralSettingController::class, 'store'])->name('store');
            // Route::get('/edit/{id}', [GeneralSettingController::class, 'edit'])->name('edit');
            // Route::put('/update/{id}', [GeneralSettingController::class, 'update'])->name('update');
            // Route::delete('/delete/{id}', [GeneralSettingController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'delivery', 'as' => 'delivery.'], function () {

            // Access module in authentication access
            Route::get('/', [DeliverySettingController::class, 'index'])->name('index');
            Route::get('/create', [DeliverySettingController::class, 'create'])->name('create');
            Route::post('/store', [DeliverySettingController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [DeliverySettingController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [DeliverySettingController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [DeliverySettingController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'rekap', 'as' => 'rekap.'], function () {
            Route::get('/', [RekapSettingController::class, 'index'])->name('index');
        });

    });

});

Route::group(['prefix' => 'globalsetting', 'as' => 'globalsetting.'], function () {
    Route::get('/', function () {
        return view('globalsetting::welcome');
    })->name('welcome');
});
