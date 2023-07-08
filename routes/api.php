<?php

use App\Http\Controllers\LebihKerjaController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LogAbsenController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::resource('karyawan', UserController::class);
Route::resource('lebihKerja', LebihKerjaController::class);
Route::resource('cuti', CutiController::class);
Route::apiResource('lembur','API\LemburController',array("as"=>"api"));
// Route::resource('lembur', LemburController::class);
Route::resource('log_absen', LogAbsenController::class);
Route::apiResource('karyawan','API\UserController',array("as"=>"api"));