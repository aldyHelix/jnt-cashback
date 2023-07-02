<?php

use Illuminate\Support\Facades\Route;

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

ladmin()->route(function() {

    Route::group(['prefix' => 'period', 'as' => 'period.'], function () {
        
        // Access module in authentication access
        
    });

});

Route::group(['prefix' => 'period', 'as' => 'period.'], function () {
    Route::get('/', function () {
        return view('period::welcome');
    })->name('welcome');
});
