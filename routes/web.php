<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route::get('/', function () {
//     return response('IT Solution');
// });

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth')->group(function(){
        Route::get('/dashboard', function () {
            return view('dashboard.dashboard');
        })->name('dashboard')->middleware('role:admin,supervisor');

        Route::get('/per-kategori', function () {
            return view('dashboard.laporan.per-kategori');
        })->name('per-kategori')->middleware('role:admin,supervisor');

        Route::get('/list-produk', function () {
            return view('dashboard.produk.produk');
        })->name('list.produk')->middleware('role:admin,supervisor');
        Route::get('/pos', function () {
            return view('pos.index');
        })->name('index')->middleware('role:kasir');
        Route::get('/riwayat-kas', function () {
            return view('dashboard.closing.riwayat-kas');
        })->name('riwayat-closing');
    });

Route::get('/outlet', function () {
    return view('dashboard.outlet.daftar-outlet');
})->name('outlet');



Route::get('/member', function () {
    return view('dashboard.user.member');
})->name('dashboard.user.member');

Route::get('/kategori', function () {
    return view('dashboard.produk.kategori-produk');
})->name('kategori');

Route::get('/riwayat-stok', function () {
    return view('dashboard.stok.riwayat-stock');
})->name('riwayat-stok');

Route::get('/transfer-stok', function () {
    return view('dashboard.stok.transfer-stok');
})->name('transfer-stok');

Route::get('/bahan-baku', function () {
    return view('dashboard.stok.bahan-baku');
})->name('bahan-baku');

Route::get('/penyesuaian-stok', function () {
    return view('dashboard.stok.penyesuaian-stok');
})->name('penyesuaian-stok');

Route::get('/stok-per-tanggal', function () {
    return view('dashboard.stok.stok-per-tanggal');
})->name('stok-per-tanggal');

Route::get('/approve-stok', function () {
    return view('dashboard.stok.approve-stok');
})->name('approve-stok');

Route::get('/perhari', function () {
    return view('dashboard.laporan.perhari');
})->name('perhari');

Route::get('/per-item', function () {
    return view('dashboard.laporan.per-item');
})->name('per-item');

Route::get('/per-member', function () {
    return view('dashboard.laporan.per-member');
})->name('per-member');



Route::get('/riwayat-transaksi', function () {
    return view('dashboard.closing.riwayat-transaksi');
})->name('riwayat-transaksi');

Route::get('/stok', function () {
    return view('dashboard.laporan.stok');
})->name('stok');

Route::get('/laporan-riwayat-stok', function () {
    return view('dashboard.laporan.riwayat-stok');
})->name('riwayat-stok');

Route::get('/laporan-bahan-baku', function () {
    return view('dashboard.laporan.bahan-baku');
})->name('bahan-baku');

Route::get('/menu-favorit', function () {
    return view('dashboard.insight.menu-favorit');
})->name('menu-favorit');

Route::get('/prediksi-penjualan', function () {
    return view('dashboard.insight.prediksi-penjualan');
})->name('prediksi-penjualan');

Route::get('/analisis-pembelian', function () {
    return view('dashboard.insight.analisis-pembelian');
})->name('analisis-pembelian');

Route::get('/laporan-approve', function () {
    return view('dashboard.laporan.approve');
})->name('approve');

Route::get('/laporan-laba', function () {
    return view('dashboard.laporan.laba');
})->name('laba');

Route::get('/template-print', function () {
    return view('dashboard.pengaturan.template-print');
})->name('template-print');

Route::get('/staff', function () {
    return view('dashboard.user.staff');
})->name('staff');

// routesfrontend