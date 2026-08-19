<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE pengaduan MODIFY koordinat_lat DECIMAL(10,6) NULL');
        DB::statement('ALTER TABLE pengaduan MODIFY koordinat_lng DECIMAL(11,6) NULL');

        DB::statement('ALTER TABLE verifikasi_lapangan MODIFY koordinat_lat DECIMAL(10,6) NULL');
        DB::statement('ALTER TABLE verifikasi_lapangan MODIFY koordinat_lng DECIMAL(11,6) NULL');

        DB::statement('ALTER TABLE penanggung_jawab_usaha MODIFY koordinat_lat DECIMAL(10,6) NULL');
        DB::statement('ALTER TABLE penanggung_jawab_usaha MODIFY koordinat_lng DECIMAL(11,6) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pengaduan MODIFY koordinat_lat DECIMAL(10,8) NULL');
        DB::statement('ALTER TABLE pengaduan MODIFY koordinat_lng DECIMAL(11,8) NULL');

        DB::statement('ALTER TABLE verifikasi_lapangan MODIFY koordinat_lat DECIMAL(10,8) NULL');
        DB::statement('ALTER TABLE verifikasi_lapangan MODIFY koordinat_lng DECIMAL(11,8) NULL');

        DB::statement('ALTER TABLE penanggung_jawab_usaha MODIFY koordinat_lat DECIMAL(10,8) NULL');
        DB::statement('ALTER TABLE penanggung_jawab_usaha MODIFY koordinat_lng DECIMAL(11,8) NULL');
    }
};
