<?php

use Illuminate\Support\Facades\Route;
use Modules\Processwizard\Http\Controllers\WizardController;

/*
|--------------------------------------------------------------------------
| Processwizard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'processwizard', 'as' => 'processwizard.'], function () {
        // Access module in authentication access
        Route::get('/', [WizardController::class, 'index'])->name('index');
        Route::get('/create', [WizardController::class, 'create'])->name('create');
        Route::post('/store', [WizardController::class, 'store'])->name('store');
    });

});
