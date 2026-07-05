<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Profil Instansi ──────────────────────────────────────────
        Schema::create('profil_instansi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi')->default('Dinas Lingkungan Hidup dan Kebersihan');
            $table->string('nama_kabupaten')->default('Kabupaten Tangerang');
            $table->string('nama_provinsi')->default('Banten');
            $table->text('alamat')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('fax', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('nama_kepala_dinas')->nullable();
            $table->string('nip_kepala_dinas', 30)->nullable();
            $table->string('jabatan_kepala_dinas')->nullable();
            $table->string('pangkat_kepala_dinas')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('kode_instansi', 20)->nullable();
            $table->string('zona_waktu', 10)->default('WIB');
            $table->timestamps();
        });

        // ── Master Kecamatan ─────────────────────────────────────────
        Schema::create('master_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->string('kode_kecamatan', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Master Kelurahan ─────────────────────────────────────────
        Schema::create('master_kelurahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('master_kecamatan')->cascadeOnDelete();
            $table->string('nama_kelurahan');
            $table->string('kode_kelurahan', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Pelapor ──────────────────────────────────────────────────
        Schema::create('pelapor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->enum('jenis_pelapor', ['perorangan', 'lembaga', 'badan_hukum'])->default('perorangan');
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('kecamatan_id')->nullable()->constrained('master_kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('master_kelurahan')->nullOnDelete();
            $table->boolean('anonim')->default(false);
            // Field tambahan untuk Lembaga / Badan Hukum
            $table->string('nama_lembaga')->nullable();
            $table->string('jabatan_di_lembaga')->nullable();
            $table->string('npwp', 25)->nullable();
            $table->timestamps();
        });

        // ── Terlapor ─────────────────────────────────────────────────
        Schema::create('terlapor', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_terlapor', ['perorangan', 'badan_hukum', 'objek_lainnya'])->default('perorangan');
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('jenis_usaha')->nullable();
            // Field tambahan untuk Badan Hukum
            $table->string('nama_perusahaan')->nullable();
            $table->string('npwp', 25)->nullable();
            $table->string('nib', 20)->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('jabatan_pj')->nullable();
            $table->timestamps();
        });

        // ── Pengaduan ────────────────────────────────────────────────
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengaduan', 30)->unique();
            $table->date('tanggal_pengaduan');
            $table->enum('sumber_laporan', ['span_lapor', 'manual'])->default('manual');
            $table->json('jenis_aduan'); // Multi-select: pencemaran_air, pencemaran_udara, dll
            $table->enum('status', [
                'pengaduan_baru',
                'menunggu_disposisi',
                'didisposisikan',
                'verifikasi_lapangan',
                'verifikasi_selesai',
                'tindak_lanjut',
                'selesai',
                'arsip',
            ])->default('pengaduan_baru');
            $table->text('uraian_pengaduan');
            $table->text('lokasi_kejadian')->nullable();
            $table->foreignId('kecamatan_id')->nullable()->constrained('master_kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('master_kelurahan')->nullOnDelete();
            $table->decimal('koordinat_lat', 10, 8)->nullable();
            $table->decimal('koordinat_lng', 11, 8)->nullable();
            $table->foreignId('pelapor_id')->constrained('pelapor')->cascadeOnDelete();
            $table->foreignId('terlapor_id')->nullable()->constrained('terlapor')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->timestamps();
        });

        // ── Disposisi ────────────────────────────────────────────────
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained('pengaduan')->cascadeOnDelete();
            $table->foreignId('pengawas_id')->constrained('users')->cascadeOnDelete();
            $table->date('jadwal_verifikasi');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Verifikasi Lapangan ──────────────────────────────────────
        Schema::create('verifikasi_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained('pengaduan')->cascadeOnDelete();
            $table->date('tanggal_verifikasi');
            $table->time('jam_verifikasi')->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->decimal('koordinat_lat', 10, 8)->nullable();
            $table->decimal('koordinat_lng', 11, 8)->nullable();
            $table->string('nama_penanggung_jawab')->nullable();
            $table->string('jabatan_pj')->nullable();
            $table->text('informasi_administrasi')->nullable();
            $table->text('fakta_temuan')->nullable();
            $table->text('saran_tindak_lanjut')->nullable();
            $table->date('tenggat_tindak_lanjut')->nullable();
            $table->string('video_path')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Tim Verifikator ──────────────────────────────────────────
        Schema::create('tim_verifikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nip', 30)->nullable();
            $table->string('pangkat', 100)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        // ── Penanggung Jawab Usaha ───────────────────────────────────
        Schema::create('penanggung_jawab_usaha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama_pj')->nullable();
            $table->string('jabatan_pj')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->text('alamat_perusahaan')->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->string('deskripsi_kegiatan')->nullable();
            $table->string('kbli', 30)->nullable();
            $table->string('nib', 30)->nullable();
            $table->string('status_permodalan', 50)->nullable();
            $table->decimal('koordinat_lat', 10, 8)->nullable();
            $table->decimal('koordinat_lng', 11, 8)->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // ── Saksi ────────────────────────────────────────────────────
        Schema::create('saksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        // ── Dokumentasi Foto ─────────────────────────────────────────
        Schema::create('dokumentasi_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path_file');
            $table->text('keterangan')->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        // ── Tanda Tangan ─────────────────────────────────────────────
        Schema::create('tanda_tangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('nip', 30)->nullable();
            $table->enum('tipe', ['verifikator', 'penanggung_jawab', 'saksi'])->default('verifikator');
            $table->text('signature_data')->nullable(); // base64 signature
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();
        });

        // ── Berita Acara ─────────────────────────────────────────────
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nomor_ba', 50)->unique();
            $table->date('tanggal_terbit');
            $table->string('qr_code_token', 64)->unique()->nullable();
            $table->string('file_pdf_path')->nullable();
            $table->string('file_docx_path')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Tindak Lanjut ────────────────────────────────────────────
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained('pengaduan')->cascadeOnDelete();
            $table->date('tanggal');
            $table->text('catatan');
            $table->text('hasil')->nullable();
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->string('dokumen_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Notifikasi ───────────────────────────────────────────────
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan')->nullable();
            $table->string('tipe', 20)->default('info'); // info, warning, success, danger
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('tindak_lanjut');
        Schema::dropIfExists('berita_acara');
        Schema::dropIfExists('tanda_tangan');
        Schema::dropIfExists('dokumentasi_foto');
        Schema::dropIfExists('saksi');
        Schema::dropIfExists('penanggung_jawab_usaha');
        Schema::dropIfExists('tim_verifikator');
        Schema::dropIfExists('verifikasi_lapangan');
        Schema::dropIfExists('disposisi');
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('terlapor');
        Schema::dropIfExists('pelapor');
        Schema::dropIfExists('master_kelurahan');
        Schema::dropIfExists('master_kecamatan');
        Schema::dropIfExists('profil_instansi');
    }
};
