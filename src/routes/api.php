<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\manejador_documentos\CorreosDtesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/summary2/factura_electronica',[CorreosDtesController::class, 'factura_electronica'])->name('factura_electronica')->withoutMiddleware(['auth', 'CSRFToken']); 
Route::post('/summary2/get_archivos',[CorreosDtesController::class, 'get_archivos'])->name('get_archivos');
Route::get('/summary2/get_list_all',[CorreosDtesController::class, 'get_list_all'])->name('get_list_all');
Route::get('/summary2/tabla_datos',[CorreosDtesController::class, 'tabla_datos'])->name('tabla_datos');