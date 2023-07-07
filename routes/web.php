<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogAbsenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LebihKerjaController;

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
Route::get('/log_absen', 'App\Http\Controllers\LogAbsenController@index');
Route::post('/log_absen/import_excel', 'App\Http\Controllers\LogAbsenController@import_excel');

Route::get('/karyawan', 'App\Http\Controllers\UserController@index');
Route::resource('/karyawan', UserController::class);
Route::delete('/delete/{id}', 'App\Http\Controllers\UserController@delete');
Route::get('/karyawan/{id}', [UserController::class], 'show')->name('show');
Route::post('/update/{id}', 'App\Http\Controllers\UserController@update');

Route::get('/lebihKerja', 'App\Http\Controllers\LebihKerjaController@index');

Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard');