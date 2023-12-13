<?php

use Illuminate\Support\Facades\Route;
use Modules\Cashbackpickup\Http\Controllers\CashbackpickupController;

/*
|--------------------------------------------------------------------------
| Cashbackpickup Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'cashbackpickup', 'as' => 'cashbackpickup.'], function () {

        // Access module in authentication access
        Route::get('/{grade}', [CashbackpickupController::class, 'index'])->name('index');
        Route::get('/denda/{id}/{grade}', [CashbackpickupController::class, 'viewDenda'])->name('view-denda');
        Route::post('/denda', [CashbackpickupController::class, 'saveDenda'])->name('denda');
        Route::get('/detail/{code}/{grade}', [CashbackpickupController::class, 'viewDetail'])->name('detail');
        Route::get('/process/{code}/{grade}/{id}', [CashbackpickupController::class, 'process'])->name('process');
        Route::get('/lock/{code}/{grade}/{id}', [CashbackpickupController::class, 'lock'])->name('lock');
        Route::get('/download/{filename}', [CashbackpickupController::class, 'downloadExcel'])->name('download');

        Route::get('/grading/dpf', [CashbackpickupController::class, 'DPFIndex'])->name('dpf.index');
        Route::get('/denda-dpf/{id}/dpf', [CashbackpickupController::class, 'viewDendaDpf'])->name('dpf.view-denda');
        Route::post('/denda-dpf/dpf', [CashbackpickupController::class, 'saveDendaDpf'])->name('dpf.save-denda');
        Route::get('/detail-dpf/{code}/dpf', [CashbackpickupController::class, 'viewDetailDpf'])->name('dpf.detail');
        Route::get('/process-dpf/{code}/dpf/{id}', [CashbackpickupController::class, 'processDpf'])->name('dpf.process');
        Route::get('/lock-dpf/{code}/dpf/{id}', [CashbackpickupController::class, 'lockDpf'])->name('dpf.lock');
    });

});

Route::group(['prefix' => 'cashbackpickup', 'as' => 'cashbackpickup.'], function () {
    Route::get('/', function () {
        return view('cashbackpickup::welcome');
    })->name('welcome');
});
