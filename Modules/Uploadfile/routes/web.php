<?php

use Illuminate\Support\Facades\Route;
use Modules\Uploadfile\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| Uploadfile Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

ladmin()->route(function () {

    Route::group(['prefix' => 'uploadfile', 'as' => 'uploadfile.'], function () {

        // Access module in authentication access
        Route::get('/', [UploadController::class, 'index'])->name('welcome');
        Route::post('/upload/process/cashback', [UploadController::class, 'uploadFile'])->name('process.cashback');
        Route::post('/upload/process/delivery', [UploadController::class, 'uploadFileDelivery'])->name('process.delivery');
    });

});

// Route::group(['prefix' => 'uploadfile', 'as' => 'uploadfile.'], function () {
//     Route::get('/', function () {
//         return view('uploadfile::welcome');
//     })->name('welcome');
// });
