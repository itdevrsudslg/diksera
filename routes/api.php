<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KepegawaianController;

Route::prefix('v1/integrasi')->middleware('api.key')->group(function () {
    Route::get('/pegawai', [KepegawaianController::class, 'getAllPegawai']);
    Route::get('/pegawai/nik/{nik}', [KepegawaianController::class, 'getPegawaiByNik']);
    Route::post('/pegawai/login', [KepegawaianController::class, 'loginPegawai']);
    // Tambahkan route ini untuk pencarian via email
    Route::get('/pegawai/email/{email}', [KepegawaianController::class, 'getPegawaiByEmail']);
});