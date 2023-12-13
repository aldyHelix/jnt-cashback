<?php

use Illuminate\Support\Facades\Route;
use Modules\Period\Http\Controllers\PeriodeController;

/*
|--------------------------------------------------------------------------
| Period Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'period', 'as' => 'period.'], function () {

        // Access module in authentication access
        Route::get('/', [PeriodeController::class, 'index'])->name('index');
        Route::post('/store', [PeriodeController::class, 'store'])->name('store');
        Route::get('/detail/{code}', [PeriodeController::class, 'viewDetail'])->name('detail');
        Route::get('/setting/{code}', [PeriodeController::class, 'viewSetting'])->name('setting');
        Route::get('/edit/{id}', [PeriodeController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PeriodeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PeriodeController::class, 'destroy'])->name('destroy');
        Route::post('/update-klien/{id}', [PeriodeController::class, 'updateKlien'])->name('update-klien');
        Route::post('/setting-dp/{id}', [PeriodeController::class, 'updateSettingDP'])->name('setting-dp');
        Route::get('/resi-checker', [PeriodeController::class, 'viewResiChecker'])->name('resi-checker');
        Route::post('/resi-checker/process', [PeriodeController::class, 'resiCheckerProcess'])->name('resi-checker-process');
    });

});

Route::group(['prefix' => 'period', 'as' => 'period.'], function () {
    Route::get('/', function () {
        return view('period::welcome');
    })->name('welcome');
});
