<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\manejador_documentos\CorreosDtesController;
use App\Http\Controllers\pruebas_server\rutControlller;
use App\Models\TestGeneral;

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

Route::get('/', function () {
    if (Auth::check()) {
        return view('Home');
    }else{
        return redirect('/login');
    }
})->name('home');

Route::get('/json_view', function () {
    if (Auth::check()) {
        return view('jsonToTable');
    }else{
        return redirect('/login');
    }
})->name('json_view');

Route::get('/pruebas_server', [rutControlller::class, 'pruebas_server']);

// Route::get('/register', [AuthController::class, 'form_registro'])->name('register');
// Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/login', [AuthController::class, 'form_login'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/producto', [TestGeneral::class, 'producto'])->name('producto.view');

/*----------------VISTAS-----------------------------*/
Route::get('/summary2/lista_facturas_f2', [CorreosDtesController::class, 'facturas_electronicas_f2'])->name('facturas_electronicas_f2');
Route::get('/summary2/lista_facturas_f3/{sub_estado}', [CorreosDtesController::class, 'facturas_electronicas_f3'])->name('facturas_electronicas_f3');
Route::get('/summary2/lista_facturas_f4', [CorreosDtesController::class, 'facturas_electronicas_f4'])->name('facturas_electronicas_f4');
Route::get('/summary2/lista_facturas_f5', [CorreosDtesController::class, 'facturas_electronicas_f5'])->name('facturas_electronicas_f5');
Route::get('/summary2/lista_facturas_f6',  [CorreosDtesController::class, 'facturas_electronicas_f6'])->name('facturas_electronicas_f6');
Route::get('/summary2/lista_facturas_f7',  [CorreosDtesController::class, 'lista_facturas_f7'])->name('lista_facturas_f7');

/*-----------------AJAX----------------------------- */
Route::post('/summary2/f1_iniciar_liquidacion', [CorreosDtesController::class, 'f1_iniciar_liquidacion'])->name('f1_iniciar_liquidacion');
Route::post('/summary2/f1_reload_liquidations', [CorreosDtesController::class, 'f1_reload_liquidations'])->name('f1_reload_liquidations');
Route::post('/summary2/valid_code_gen', [CorreosDtesController::class, 'valid_code_gen'])->name('valid_code_gen');
Route::post('/summary2/f1_asignar_factura', [CorreosDtesController::class, 'f1_asignar_factura'])->name('asignar_factura');
Route::post('/summary2/f1_enviar', [CorreosDtesController::class, 'f1_enviar'])->name('f1_enviar');
Route::get('/summary2/ver_impuestos_factura',[CorreosDtesController::class, 'ver_impuestos'])->name('ver_impuestos');
Route::post('/summary2/f1_deshabilitar_factura', [CorreosDtesController::class, 'f1_deshabilitar_factura'])->name('f1_deshabilitar_factura');

Route::post('/summary2/f0_enviar_factura', [CorreosDtesController::class, 'f0_enviar_factura'])->name('f0_enviar_factura');
Route::get('/summary2/f2_reload_facturas_from_liqu', [CorreosDtesController::class, 'f2_reload_facturas_from_liqu'])->name('f2_reload_facturas_from_liqu');
Route::post('/summary2/f2_enviar', [CorreosDtesController::class, 'f2_enviar'])->name('f2_enviar');
Route::post('/summary2/f3_registro_num_entrada', [CorreosDtesController::class, 'f3_registro_num_entrada'])->name('f3_registro_num_entrada');
Route::post('/summary2/f5_registro_diario', [CorreosDtesController::class, 'f5_registro_diario'])->name('f5_registro_diario');
Route::post('/summary2/asignar_empleado', [CorreosDtesController::class, 'asignar_empleado'])->name('asignar_empleado');
Route::post('/summary2/f5_enviar',[CorreosDtesController::class, 'f5_enviar'])->name('f5_enviar');
Route::post('/summary2/fx_registro_extra', [CorreosDtesController::class, 'fx_registro_extra'])->name('fx_registro_extra');
Route::post('/summary2/upload_pdf_json', [CorreosDtesController::class, 'upload_pdf_json'])->name('upload_pdf_json');
Route::get('/summary2/f6_marca_final/{id_dte}/{tipo}', [CorreosDtesController::class, 'f6_marca_final'])->name('f6_marca_final');




/*------------------ API ----------------------------*/

