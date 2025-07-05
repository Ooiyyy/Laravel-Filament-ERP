<?php

use App\Http\Controllers\Laporan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanPembayaranController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/laporan-pembayaran', [LaporanPembayaranController::class, 'cetak'])
    ->name('laporan-pembayaran.cetak');
