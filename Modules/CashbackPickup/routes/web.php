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
        Route::get('/denda/{id}/{grade}', [CashbackPickupController::class, 'viewDenda'])->name('view-denda');
        Route::post('/denda', [CashbackPickupController::class, 'saveDenda'])->name('denda');
        Route::get('/detail/{code}/{grade}', [CashbackPickupController::class, 'viewDetail'])->name('detail');
        Route::get('/process/{code}/{grade}/{id}', [CashbackPickupController::class, 'process'])->name('process');
        Route::get('/lock/{code}/{grade}/{id}', [CashbackPickupController::class, 'lock'])->name('lock');
        Route::get('/download/{filename}', [CashbackPickupController::class, 'downloadExcel'])->name('download');

        Route::get('/grading/dpf', [CashbackPickupController::class, 'DPFIndex'])->name('dpf.index');
        Route::get('/denda-dpf/{id}/dpf', [CashbackPickupController::class, 'viewDendaDpf'])->name('dpf.view-denda');
        Route::post('/denda-dpf/dpf', [CashbackPickupController::class, 'saveDendaDpf'])->name('dpf.save-denda');
        Route::get('/detail-dpf/{code}/dpf', [CashbackPickupController::class, 'viewDetailDpf'])->name('dpf.detail');
        Route::get('/process-dpf/{code}/dpf/{id}', [CashbackPickupController::class, 'processDpf'])->name('dpf.process');
        Route::get('/lock-dpf/{code}/dpf/{id}', [CashbackPickupController::class, 'lockDpf'])->name('dpf.lock');
    });

});

Route::group(['prefix' => 'cashbackpickup', 'as' => 'cashbackpickup.'], function () {
    Route::get('/', function () {
        return view('cashbackpickup::welcome');
    })->name('welcome');
});
