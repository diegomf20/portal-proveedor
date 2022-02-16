<?php

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
use App\Http\Controllers\UserController;
Route::resource('user',UserController::class);
Route::post('login',[UserController::class,'login']);
Route::get('rutas',[UserController::class,'rutas']);


use App\Http\Controllers\DocumentoController;
Route::get('documento/pendientes',[DocumentoController::class,'pendientes']);
Route::resource('documento',DocumentoController::class);