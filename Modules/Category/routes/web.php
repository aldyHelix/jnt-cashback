<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Category Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function() {

    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        
        // Access module in authentication access
        
    });

});

Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
    Route::get('/', function () {
        return view('category::welcome');
    })->name('welcome');
});
