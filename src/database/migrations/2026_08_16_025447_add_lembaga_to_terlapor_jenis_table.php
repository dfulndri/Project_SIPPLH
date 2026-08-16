<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah opsi 'lembaga' ke enum jenis_terlapor (raw SQL, karena Laravel
        // tidak punya cara bawaan untuk ubah daftar nilai enum di MySQL)
        DB::statement("
            ALTER TABLE terlapor
            MODIFY jenis_terlapor ENUM('perorangan', 'lembaga', 'badan_hukum', 'objek_lainnya')
            NOT NULL DEFAULT 'perorangan'
        ");
    }

    public function down(): void
    {
        // Kembalikan ke 3 opsi semula. Catatan: kalau ada data ber-jenis 'lembaga'
        // saat rollback, baris itu akan gagal/berubah — pastikan tidak ada data begitu
        // sebelum rollback.
        DB::statement("
            ALTER TABLE terlapor
            MODIFY jenis_terlapor ENUM('perorangan', 'badan_hukum', 'objek_lainnya')
            NOT NULL DEFAULT 'perorangan'
        ");
    }
};
