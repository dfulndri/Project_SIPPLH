<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_kbli', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kbli', 10)->unique();
            $table->string('judul', 500);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->fullText('judul'); // biar pencarian keyword ("kertas") cepat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_kbli');
    }
};
