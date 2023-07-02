<?php

use Illuminate\Support\Facades\Route;
use Modules\CollectionPoint\Http\Controllers\CollectionPointController;

/*
|--------------------------------------------------------------------------
| CollectionPoint Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function() {

    Route::group(['prefix' => 'collectionpoint', 'as' => 'collectionpoint.'], function () {

        // Access module in authentication access
        Route::get('/', [CollectionPointController::class, 'index'])->name('index');
        Route::get('/create', [CollectionPointController::class, 'create'])->name('create');
        Route::post('/store', [CollectionPointController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CollectionPointController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CollectionPointController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CollectionPointController::class, 'destroy'])->name('destroy');
    });

});

Route::group(['prefix' => 'collectionpoint', 'as' => 'collectionpoint.'], function () {
    Route::get('/', function () {
        return view('collectionpoint::welcome');
    })->name('welcome');
});
