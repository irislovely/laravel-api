<?php

use App\Http\Controllers\FilesController;
use App\Http\Controllers\ProvidersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/providers', [ProvidersController::class,'index'])->name('providers.index');

Route::get('/files', [FilesController::class,'index'])->name('files.index');
Route::post('/files', [FilesController::class,'store'])->name('files.store');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
