<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryKlienPengirimanController;

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
        Route::get('/', [CategoryKlienPengirimanController::class, 'index'])->name('index');
        Route::get('/create', [CategoryKlienPengirimanController::class, 'create'])->name('create');
        Route::post('/store', [CategoryKlienPengirimanController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CategoryKlienPengirimanController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CategoryKlienPengirimanController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CategoryKlienPengirimanController::class, 'destroy'])->name('destroy');

        Route::post('/sync', [CategoryKlienPengirimanController::class, 'syncKlienPengiriman'])->name('sync');
        Route::post('/save-setting', [CategoryKlienPengirimanController::class, 'saveSetting'])->name('savesetting');
    });

});

// Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
//     Route::get('/', function () {
//         return view('category::welcome');
//     })->name('welcome');
// });
