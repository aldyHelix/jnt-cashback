<?php

use Illuminate\Support\Facades\Route;
use Modules\UploadFile\Http\Controllers\UploadController;
use App\Http\Controllers\QueueWorkerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/start-workers', [QueueWorkerController::class, 'startWorkers']);

Route::get('/', function () {
    return redirect()->route('ladmin.admin.index');
});


