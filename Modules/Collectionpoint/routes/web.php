<?php

use Illuminate\Support\Facades\Route;
use Modules\Collectionpoint\Http\Controllers\CollectionpointController;

/*
|--------------------------------------------------------------------------
| Collectionpoint Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'collectionpoint', 'as' => 'collectionpoint.'], function () {

        // Access module in authentication access
        Route::get('/', [CollectionpointController::class, 'index'])->name('index');
        Route::get('/create', [CollectionpointController::class, 'create'])->name('create');
        Route::post('/store', [CollectionpointController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CollectionpointController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CollectionpointController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CollectionpointController::class, 'destroy'])->name('destroy');
    });

});

Route::group(['prefix' => 'collectionpoint', 'as' => 'collectionpoint.'], function () {
    Route::get('/', function () {
        return view('collectionpoint::welcome');
    })->name('welcome');
});
