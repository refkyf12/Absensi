<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogAbsenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LebihKerjaController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\LogKegiatanController;
use App\Http\Controllers\JamKurangController;
use App\Http\Controllers\SoapController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\LiburNasionalController;
use App\Http\Controllers\AkumulasiTahunanController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\AbsenNonKerjaController;
use App\Http\Controllers\KetidakhadiranController;


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
    return view('welcome');
});

Route::post('/soap_activity', 'App\Http\Controllers\LogActivityController@store');
Route::post('/soap_data', 'App\Http\Controllers\SoapController@logAbsenStore');
Route::post('/soap_non_kerja', 'App\Http\Controllers\AbsenNonKerjaController@logAbsenNonKerja');
Route::post('/soap_data_july', 'App\Http\Controllers\SoapController@getJulyData');
Route::get('/log_activity', 'App\Http\Controllers\LogActivityController@index');


Route::get('/reset', 'App\Http\Controllers\UserController@reset'); 
Route::get('/login', 'App\Http\Controllers\UserController@loginview');
Route::post('/authenticate', 'App\Http\Controllers\UserController@login');
Route::post('/logout', 'App\Http\Controllers\UserController@logout');
Route::get('/logout', 'App\Http\Controllers\UserController@logout');
//Route::get('log_absen', [LogAbsenController::class, 'index'])->name('log_absen.index');
Route::get('/log_absen', 'App\Http\Controllers\LogAbsenController@index');
Route::get('/log_absen/edit/{id}', 'App\Http\Controllers\LogAbsenController@show_edit');
Route::post('/log_absen/edit/berhasil/{id}', 'App\Http\Controllers\LogAbsenController@edit');
Route::get('/filter','App\Http\Controllers\LogAbsenController@filter');

Route::get('/absen_non_kerja', 'App\Http\Controllers\AbsenNonKerjaController@index');

Route::post('/log_absen/import_excel', 'App\Http\Controllers\LogAbsenController@import_excel');

Route::get('/karyawan', 'App\Http\Controllers\UserController@index');
Route::get('/karyawan/create', 'App\Http\Controllers\UserController@create');
Route::post('/karyawan/create/store', 'App\Http\Controllers\UserController@store');
Route::delete('/delete/{id}', 'App\Http\Controllers\UserController@delete');
Route::get('/karyawan/{id}', 'App\Http\Controllers\UserController@show');
Route::post('/karyawan/update/{id}', 'App\Http\Controllers\UserController@update');
Route::get('/kurang/{id}', 'App\Http\Controllers\UserController@lebihKurangLembur');
Route::post('/karyawan/lemburKeCuti/{id}', 'App\Http\Controllers\UserController@lemburKeCuti');
Route::post('/karyawan/kurangKurangCuti/{id}', 'App\Http\Controllers\UserController@jamKurangMinusCuti');

Route::get('/lebihKerja', 'App\Http\Controllers\LebihKerjaController@index');
Route::get('/kurangKerja', 'App\Http\Controllers\JamKurangController@index');


Route::get('/rules', 'App\Http\Controllers\RulesController@index');
Route::get('/rules/{id}', 'App\Http\Controllers\RulesController@show');
Route::post('/rules/update/{id}', 'App\Http\Controllers\RulesController@update');

// Route::resource('lembur', LemburController::class);
Route::get('/lembur', 'App\Http\Controllers\LemburController@index');
Route::get('/lembur/create', 'App\Http\Controllers\LemburController@create');
Route::get('/lembur/filter','App\Http\Controllers\LemburController@filter');
Route::post('/lembur/create', 'App\Http\Controllers\LemburController@store');
Route::get('/lembur/status/{id}', 'App\Http\Controllers\LemburController@show_approval');
Route::post('/lembur/status/update/{id}', 'App\Http\Controllers\LemburController@approval');
Route::get('/lembur/status/viewedit/{id}', 'App\Http\Controllers\LemburController@show_edit');
Route::post('/lembur/status/edit/{id}', 'App\Http\Controllers\LemburController@edit');
Route::get('/delete_lembur/{id}', 'App\Http\Controllers\LemburController@delete');

Route::get('/libur', 'App\Http\Controllers\LiburNasionalController@index');
Route::get('/libur/create', 'App\Http\Controllers\LiburNasionalController@create');
Route::post('/libur/create/store', 'App\Http\Controllers\LiburNasionalController@store');
Route::get('/libur/{id}', 'App\Http\Controllers\LiburNasionalController@show');
Route::post('/libur/update/{id}', 'App\Http\Controllers\LiburNasionalController@update');

Route::get('/akumulasi_tahunan', 'App\Http\Controllers\AkumulasiTahunanController@index');

Route::get('/log_kegiatan', 'App\Http\Controllers\LogKegiatanController@index');

// Route::resource('cuti', CutiController::class);
Route::get('/cuti', 'App\Http\Controllers\CutiController@index');
Route::get('/cuti/status/{id}', 'App\Http\Controllers\CutiController@show_approval');
Route::post('/cuti/status/update/{id}', 'App\Http\Controllers\CutiController@approval');
Route::get('/cuti/create', 'App\Http\Controllers\CutiController@create');
Route::post('/cuti/create/store', 'App\Http\Controllers\CutiController@store');
Route::get('/cuti/filter','App\Http\Controllers\CutiController@filter');

Route::get('/role', 'App\Http\Controllers\RoleController@index');
Route::get('/role/create', 'App\Http\Controllers\RoleController@create');
Route::post('/role/create/store', 'App\Http\Controllers\RoleController@store');
Route::get('/role/{id}', 'App\Http\Controllers\RoleController@show');
Route::post('/role/update/{id}', 'App\Http\Controllers\RoleController@update');

Route::get('/akumulasi', 'App\Http\Controllers\LogAbsenController@indexAkumulasi');
Route::get('/akumulasi/filter', 'App\Http\Controllers\LogAbsenController@akumulasiFilter');
Route::get('/akumulasi/detail/{id}', 'App\Http\Controllers\LogAbsenController@showDetailLogAbsen');

Route::get('/akumulasiLembur', 'App\Http\Controllers\LemburController@indexAkumulasiLembur');
Route::get('/akumulasiLembur/filter', 'App\Http\Controllers\LemburController@akumulasiLembur');
Route::get('/akumulasiLembur/detail/{id}', 'App\Http\Controllers\LemburController@showDetailLembur');

Route::get('/ketidakhadiran', 'App\Http\Controllers\KetidakhadiranController@index');
Route::post('/ketidakhadiran/simpan', 'App\Http\Controllers\KetidakhadiranController@store');
Route::get('/ketidakhadiran/show/{id}', 'App\Http\Controllers\KetidakhadiranController@show_edit');
Route::post('/ketidakhadiran/update/{id}', 'App\Http\Controllers\KetidakhadiranController@edit');