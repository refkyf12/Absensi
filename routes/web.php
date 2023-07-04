<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogAbsenController;
use App\Http\Controllers\UserController;

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
Route::get('/logout', 'App\Http\Controllers\UserController@logout');
Route::get('/log_absen', 'App\Http\Controllers\LogAbsenController@index');
Route::post('/log_absen/import_excel', 'App\Http\Controllers\LogAbsenController@import_excel');
