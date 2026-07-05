<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\Admin\DisposisiController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Admin\BeritaAcaraController;
use App\Http\Controllers\Admin\TindakLanjutController;
use App\Http\Controllers\Admin\ArsipController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PelaporController;
use App\Http\Controllers\Admin\TerlaporController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\Admin\ProfilInstansiController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // ── Dashboard ────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart/{tahun}', [DashboardController::class, 'chartData'])->name('dashboard.chart');

    // ── Pengaduan ────────────────────────────────────────────────
    Route::resource('pengaduan', PengaduanController::class);
    Route::patch('pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.status');

    // ── Disposisi ────────────────────────────────────────────────
    Route::get('disposisi',                  [DisposisiController::class, 'index'])->name('disposisi.index');
    Route::get('disposisi/create',           [DisposisiController::class, 'create'])->name('disposisi.create');
    Route::post('disposisi',                 [DisposisiController::class, 'store'])->name('disposisi.store');
    Route::get('disposisi/{disposisi}',      [DisposisiController::class, 'show'])->name('disposisi.show');

    // ── Verifikasi Lapangan ──────────────────────────────────────
    Route::resource('verifikasi', VerifikasiController::class);
    Route::post('verifikasi/{verifikasi}/finalize', [VerifikasiController::class, 'finalize'])->name('verifikasi.finalize');
    Route::delete('verifikasi/{verifikasi}/foto/{foto}', [VerifikasiController::class, 'deleteFoto'])->name('verifikasi.foto.delete');

    // ── Berita Acara ─────────────────────────────────────────────
    Route::get('berita-acara',                  [BeritaAcaraController::class, 'index'])->name('berita-acara.index');
    Route::get('berita-acara/{ba}',             [BeritaAcaraController::class, 'show'])->name('berita-acara.show');
    Route::patch('berita-acara/{ba}/finalize',  [BeritaAcaraController::class, 'finalize'])->name('berita-acara.finalize');
    Route::get('berita-acara/{ba}/pdf',         [BeritaAcaraController::class, 'downloadPdf'])->name('berita-acara.pdf');

    // ── Tindak Lanjut ────────────────────────────────────────────
    Route::get('tindak-lanjut',                       [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
    Route::get('tindak-lanjut/create',                [TindakLanjutController::class, 'create'])->name('tindak-lanjut.create');
    Route::post('tindak-lanjut',                      [TindakLanjutController::class, 'store'])->name('tindak-lanjut.store');
    Route::patch('tindak-lanjut/{tindakLanjut}/selesai', [TindakLanjutController::class, 'selesai'])->name('tindak-lanjut.selesai');

    // ── Arsip ────────────────────────────────────────────────────
    Route::get('arsip',                       [ArsipController::class, 'index'])->name('arsip.index');
    Route::patch('arsip/{pengaduan}/archive', [ArsipController::class, 'archive'])->name('arsip.archive');

    // ── Laporan ──────────────────────────────────────────────────
    Route::get('laporan',              [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('laporan/export-pdf',   [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

    // ── Master Data: Pelapor ─────────────────────────────────────
    Route::get('pelapor',                  [PelaporController::class, 'index'])->name('pelapor.index');
    Route::post('pelapor',                 [PelaporController::class, 'store'])->name('pelapor.store');
    Route::patch('pelapor/{pelapor}',      [PelaporController::class, 'update'])->name('pelapor.update');
    Route::delete('pelapor/{pelapor}',     [PelaporController::class, 'destroy'])->name('pelapor.destroy');

    // ── Master Data: Terlapor ────────────────────────────────────
    Route::get('terlapor',                 [TerlaporController::class, 'index'])->name('terlapor.index');
    Route::post('terlapor',                [TerlaporController::class, 'store'])->name('terlapor.store');
    Route::patch('terlapor/{terlapor}',    [TerlaporController::class, 'update'])->name('terlapor.update');
    Route::delete('terlapor/{terlapor}',   [TerlaporController::class, 'destroy'])->name('terlapor.destroy');

    // ── Manajemen Wilayah ────────────────────────────────────────
    Route::get('wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('wilayah/kecamatan',               [WilayahController::class, 'storeKecamatan'])->name('wilayah.kecamatan.store');
    Route::patch('wilayah/kecamatan/{kecamatan}',  [WilayahController::class, 'updateKecamatan'])->name('wilayah.kecamatan.update');
    Route::delete('wilayah/kecamatan/{kecamatan}', [WilayahController::class, 'destroyKecamatan'])->name('wilayah.kecamatan.destroy');
    Route::post('wilayah/kelurahan',               [WilayahController::class, 'storeKelurahan'])->name('wilayah.kelurahan.store');
    Route::patch('wilayah/kelurahan/{kelurahan}',   [WilayahController::class, 'updateKelurahan'])->name('wilayah.kelurahan.update');
    Route::delete('wilayah/kelurahan/{kelurahan}',  [WilayahController::class, 'destroyKelurahan'])->name('wilayah.kelurahan.destroy');

    // ── Manajemen User ───────────────────────────────────────────
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle');

    // ── Profil Instansi ──────────────────────────────────────────
    Route::get('profil-instansi',  [ProfilInstansiController::class, 'edit'])->name('profil-instansi.edit');
    Route::patch('profil-instansi', [ProfilInstansiController::class, 'update'])->name('profil-instansi.update');

    // ── AJAX: kelurahan by kecamatan ─────────────────────────────
    Route::get('master/kelurahan-json/{kecamatan_id}', function ($kecId) {
        return \App\Models\MasterKelurahan::where('kecamatan_id', $kecId)
            ->where('is_active', true)->orderBy('nama_kelurahan')
            ->get(['id', 'nama_kelurahan']);
    })->name('master.kelurahan.json');
});
