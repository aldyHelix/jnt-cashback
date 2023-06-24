<?php

use Illuminate\Support\Facades\Route;

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
        
    });

});

Route::group(['prefix' => 'cashbackpickup', 'as' => 'cashbackpickup.'], function () {
    Route::get('/', function () {
        return view('cashbackpickup::welcome');
    })->name('welcome');
});
