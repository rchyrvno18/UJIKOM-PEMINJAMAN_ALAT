<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\AlatController;
use App\Http\Controllers\API\PeminjamanController;

Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);


Route::middleware(['auth:sanctum', 'role:petugas'])->group(function () {
    Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
});

Route::middleware(['auth:sanctum', 'role:peminjam'])->group(function () {
    Route::post('/peminjaman', [PeminjamanController::class, 'store']);
    Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('alat', AlatController::class);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('kategori', KategoriController::class);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/me', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Data profil berhasil diambil.',
        'data' => new \App\Http\Resources\UserResource($request->user()),
    ]);
})->middleware('auth:sanctum');

Route::get('/pengembalian', [PengembalianController::class, 'index']);
Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
Route::put('/pengembalian/{pengembalian', [PengembalianController::class, 'update']);
Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);

Route::post('/pengembalian', [PengembalianController::class, 'store']);

Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);