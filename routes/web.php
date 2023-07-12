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


Route::get('/reset', 'App\Http\Controllers\UserController@reset'); 
Route::get('/login', 'App\Http\Controllers\UserController@loginview');
Route::post('/authenticate', 'App\Http\Controllers\UserController@login');
Route::post('/logout', 'App\Http\Controllers\UserController@logout');
Route::get('/logout', 'App\Http\Controllers\UserController@logout');
//Route::get('log_absen', [LogAbsenController::class, 'index'])->name('log_absen.index');
Route::get('/log_absen', 'App\Http\Controllers\LogAbsenController@index');
Route::get('/filter','App\Http\Controllers\LogAbsenController@filter');

Route::post('/log_absen/import_excel', 'App\Http\Controllers\LogAbsenController@import_excel');

Route::get('/karyawan', 'App\Http\Controllers\UserController@index');
Route::get('/karyawan/create', 'App\Http\Controllers\UserController@create');
Route::post('/karyawan/create/store', 'App\Http\Controllers\UserController@store');
Route::delete('/delete/{id}', 'App\Http\Controllers\UserController@delete');
Route::get('/karyawan/{id}', 'App\Http\Controllers\UserController@show');
Route::post('/update/{id}', 'App\Http\Controllers\UserController@update');
Route::get('/kurang/{id}', 'App\Http\Controllers\UserController@lebihKurangLembur');

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
Route::get('/delete_lembur/{id}', 'App\Http\Controllers\LemburController@delete');

Route::get('/log_kegiatan', 'App\Http\Controllers\LogKegiatanController@index');

Route::post('/soap_data', 'App\Http\Controllers\SoapController@logAbsenStore');

// Route::resource('cuti', CutiController::class);
Route::get('/cuti', 'App\Http\Controllers\CutiController@index');
Route::get('/cuti/status/{id}', 'App\Http\Controllers\CutiController@show_approval');
Route::post('/cuti/status/update/{id}', 'App\Http\Controllers\CutiController@approval');
Route::get('/cuti/create', 'App\Http\Controllers\CutiController@create');
Route::post('/cuti/create/store', 'App\Http\Controllers\CutiController@store');
Route::get('/cuti/filter','App\Http\Controllers\CutiController@filter');

Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');