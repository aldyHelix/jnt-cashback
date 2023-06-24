<?php

use Illuminate\Support\Facades\Route;
use Modules\UploadFile\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| UploadFile Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function() {

    Route::group(['prefix' => 'uploadfile', 'as' => 'uploadfile.'], function () {

        // Access module in authentication access
        Route::get('/', [UploadController::class, 'index'])->name('welcome');
        Route::post('/upload/process', [UploadController::class, 'uploadFile'])->name('process');
    });

});

// Route::group(['prefix' => 'uploadfile', 'as' => 'uploadfile.'], function () {
//     Route::get('/', function () {
//         return view('uploadfile::welcome');
//     })->name('welcome');
// });
