<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Delivery Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function() {

    Route::group(['prefix' => 'delivery', 'as' => 'delivery.'], function () {
        
        // Access module in authentication access
        
    });

});

Route::group(['prefix' => 'delivery', 'as' => 'delivery.'], function () {
    Route::get('/', function () {
        return view('delivery::welcome');
    })->name('welcome');
});
