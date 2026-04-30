<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\PenghuniRumahController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RumahController;

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

Route::get('/penghuni', [PenghuniController::class, 'index']);
Route::post('/penghuni/rumah', [PenghuniRumahController::class, 'penghuni']);

//PEMBAYARAN
Route::post('/pembayaran', [PembayaranController::class, 'store']);

//PENGELUARAN
Route::get('/pengeluaran/total', [PengeluaranController::class, 'summary']);
Route::post('tambah/pengeluaran', [PengeluaranController::class, 'store']);
Route::get('/report/detail', [PengeluaranController::class, 'detail']);
Route::get('/report/grafik', [PengeluaranController::class, 'grafik']);

//RUMAH
Route::get('/rumah', [RumahController::class, 'index']);
Route::post('/rumah', [RumahController::class, 'store']);
Route::get('/rumah/{id}', [RumahController::class, 'show']);
Route::put('/rumah/{id}', [RumahController::class, 'update']);
Route::delete('/rumah/{id}', [RumahController::class, 'destroy']);

