<?php

use Illuminate\Support\Facades\Route;
use Modules\Ratesetting\Http\Controllers\DeliveryFeeController;
use Modules\Ratesetting\Http\Controllers\RatesettingController;

/*
|--------------------------------------------------------------------------
| Ratesetting Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'ratesetting', 'as' => 'ratesetting.'], function () {

        // Access module in authentication access
        Route::get('grade-a/', [RatesettingController::class, 'indexGradeA'])->name('grade-a.index');
        Route::get('grade-b/', [RatesettingController::class, 'indexGradeB'])->name('grade-b.index');
        Route::get('grade-c/', [RatesettingController::class, 'indexGradeC'])->name('grade-c.index');
        Route::get('grade/{grade}', [RatesettingController::class, 'create'])->name('grade.create');
        Route::get('grade/edit/{grade}/{id}', [RatesettingController::class, 'edit'])->name('grade.edit');
        Route::post('grade/store', [RatesettingController::class, 'store'])->name('grade.store');
        Route::put('grade/update/{id}', [RatesettingController::class, 'update'])->name('grade.update');
        Route::delete('grade/destroy/{grade}/{id}', [RatesettingController::class, 'destroy'])->name('grade.destroy');

        //  Access delivery fee
        Route::get('deliveryfee/', [DeliveryFeeController::class, 'index'])->name('delivery.index');
        Route::get('deliveryfee/create', [DeliveryFeeController::class, 'create'])->name('delivery.create');
        Route::get('deliveryfee/edit/{id}', [DeliveryFeeController::class, 'edit'])->name('delivery.edit');
        Route::post('deliveryfee/store', [DeliveryFeeController::class, 'store'])->name('delivery.store');
        Route::put('deliveryfee/update/{id}', [DeliveryFeeController::class, 'update'])->name('delivery.update');
        Route::delete('deliveryfee/delete/{id}', [DeliveryFeeController::class, 'delete'])->name('delivery.destroy');
        Route::post('deliveryfee/setting', [DeliveryFeeController::class, 'settingZona'])->name('delivery.setting');
    });

});

Route::group(['prefix' => 'ratesetting', 'as' => 'ratesetting.'], function () {
    Route::get('/', function () {
        return view('ratesetting::welcome');
    })->name('welcome');
});
