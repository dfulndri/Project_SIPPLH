<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penanggung_jawab_usaha', function (Blueprint $table) {
            $table->foreignId('kbli_id')->nullable()->after('kbli')
                ->constrained('master_kbli')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penanggung_jawab_usaha', function (Blueprint $table) {
            $table->dropForeign(['kbli_id']);
            $table->dropColumn('kbli_id');
        });
    }
};
