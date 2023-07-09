<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogAbsenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LebihKerjaController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\RulesController;

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

Route::get('/dashboard', 'App\Http\Controllers\UserController@dashboard'); 
Route::get('/login', 'App\Http\Controllers\UserController@loginview');
Route::post('/authenticate', 'App\Http\Controllers\UserController@login');
Route::post('/logout', 'App\Http\Controllers\UserController@logout');
Route::get('/logout', 'App\Http\Controllers\UserController@logout');
Route::get('log_absen', [LogAbsenController::class, 'index'])->name('log_absen.index');
Route::get('/filter','App\Http\Controllers\LogAbsenController@filter');

Route::post('/log_absen/import_excel', 'App\Http\Controllers\LogAbsenController@import_excel');

Route::get('/karyawan', 'App\Http\Controllers\UserController@index');
Route::resource('/karyawan', UserController::class);
Route::delete('/delete/{id}', 'App\Http\Controllers\UserController@delete');
Route::get('/karyawan/{id}', [UserController::class], 'show')->name('show');
Route::post('/update/{id}', 'App\Http\Controllers\UserController@update');

Route::get('/lebihKerja', 'App\Http\Controllers\LebihKerjaController@index');

Route::get('/rules', 'App\Http\Controllers\RulesController@index');
Route::get('/rules/{id}', 'App\Http\Controllers\RulesController@show');
Route::post('/rules/update/{id}', 'App\Http\Controllers\RulesController@update');

// Route::resource('lembur', LemburController::class);
Route::get('/lembur', 'App\Http\Controllers\LemburController@index');
Route::get('/lembur/create', 'App\Http\Controllers\LemburController@create');
Route::get('/lembur/filter','App\Http\Controllers\LemburController@filter');
Route::post('/lembur/create', 'App\Http\Controllers\LemburController@store');
Route::get('/delete_lembur/{id}', 'App\Http\Controllers\LemburController@delete');

// Route::resource('cuti', CutiController::class);
Route::get('/cuti', 'App\Http\Controllers\CutiController@index');
Route::get('/cuti/filter','App\Http\Controllers\CutiController@filter');

Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');