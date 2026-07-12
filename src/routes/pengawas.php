<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pengawas\DashboardController;
use App\Http\Controllers\Pengawas\TugasController;
use App\Http\Controllers\Pengawas\VerifikasiController;
use App\Http\Controllers\Pengawas\BeritaAcaraController;
use App\Http\Controllers\Pengawas\TindakLanjutController;
use App\Http\Controllers\Pengawas\ProfilController;

Route::prefix('pengawas')
    ->name('pengawas.')
    ->middleware(['auth', 'pengawas'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Pengaduan Saya (Tugas) ───────────────────────────────
        Route::get('tugas',             [TugasController::class, 'index'])->name('tugas.index');
        Route::get('tugas/{pengaduan}', [TugasController::class, 'show'])->name('tugas.show');

        // ── Verifikasi Lapangan ──────────────────────────────────
        Route::get('verifikasi',                     [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('verifikasi/create',              [VerifikasiController::class, 'create'])->name('verifikasi.create');
        Route::post('verifikasi',                    [VerifikasiController::class, 'store'])->name('verifikasi.store');
        Route::get('verifikasi/{verifikasi}/edit',   [VerifikasiController::class, 'edit'])->name('verifikasi.edit');
        Route::patch('verifikasi/{verifikasi}',      [VerifikasiController::class, 'update'])->name('verifikasi.update');
        Route::post('verifikasi/{verifikasi}/foto',  [VerifikasiController::class, 'uploadFoto'])->name('verifikasi.foto');
        Route::delete('verifikasi/{verifikasi}/foto/{foto}', [VerifikasiController::class, 'deleteFoto'])->name('verifikasi.foto.delete');
        Route::patch('verifikasi/{verifikasi}/finalize', [VerifikasiController::class, 'finalize'])->name('verifikasi.finalize');

        // ── Berita Acara ─────────────────────────────────────────
        Route::get('berita-acara',          [BeritaAcaraController::class, 'index'])->name('berita-acara.index');
        Route::get('berita-acara/{ba}',     [BeritaAcaraController::class, 'show'])->name('berita-acara.show');
        Route::get('berita-acara/{ba}/pdf', [BeritaAcaraController::class, 'downloadPdf'])->name('berita-acara.pdf');

        // ── Tindak Lanjut ────────────────────────────────────────
        Route::get('tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');

        // ── Profil ───────────────────────────────────────────────
        Route::get('profil',   [ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('profil', [ProfilController::class, 'update'])->name('profil.update');
    });
