<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// ── Redirect root ─────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ── Auth ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',          [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',         [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin ─────────────────────────────────────────────────────
require __DIR__ . '/admin.php';

// ── Pengawas ──────────────────────────────────────────────────
require __DIR__ . '/pengawas.php';

// ── Validasi QR Code BA (publik) ──────────────────────────────
Route::get('/verify/{token}', function ($token) {
    $ba = \App\Models\BeritaAcara::where('qr_code_token', $token)
        ->with('verifikasi.pengaduan.terlapor')
        ->firstOrFail();
    return view('public.verify-ba', compact('ba'));
})->name('ba.verify');
