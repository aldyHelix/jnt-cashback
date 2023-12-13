<?php

use Illuminate\Support\Facades\Route;
use Modules\Droppointoutgoing\Http\Controllers\DropPointController;

/*
|--------------------------------------------------------------------------
| Droppointoutgoing Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'droppointoutgoing', 'as' => 'droppointoutgoing.'], function () {

        // Access module in authentication access
        Route::get('/', [DropPointController::class, 'index'])->name('index');
        Route::post('/sync', [DropPointController::class, 'syncDroppointoutgoing'])->name('sync');
    });

});
