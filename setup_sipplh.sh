#!/bin/bash
# ════════════════════════════════════════════════════════════════════
#  SIPPLH — Backend Foundation Setup Script
#  Meliputi: Migrations · Models · Routes · Middleware · Seeder
#
#  Cara pakai:
#    1. Taruh file ini di folder project_sipplh/
#    2. Jalankan: bash setup_sipplh.sh
# ════════════════════════════════════════════════════════════════════

set -e

MIG="src/database/migrations"
MODEL="src/app/Models"
MW="src/app/Http/Middleware"
ROUTES="src/routes"

G='\033[0;32m'; Y='\033[1;33m'; B='\033[1;34m'; NC='\033[0m'
ok()   { echo -e "  ${G}✓${NC} $1"; }
hdr()  { echo ""; echo -e "${B}━━━ $1 ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"; echo ""; }
mig()  { ls "${MIG}"/*"$1"* 2>/dev/null | head -1 || true; }

echo ""
echo "╔═════════════════════════════════════════════════════╗"
echo "║   SIPPLH — Migrations, Models, Routes, Seeder       ║"
echo "╚═════════════════════════════════════════════════════╝"

# ════════════════════════════════════════════════════════════════
# BAGIAN 1: MIGRATIONS
# ════════════════════════════════════════════════════════════════
hdr "[1/5] Writing Migrations"

# ── 1. USERS ────────────────────────────────────────────────────
F=$(mig "create_users_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "users"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'pengawas', 'viewer'])->default('viewer');
            $table->string('nip', 30)->nullable();
            $table->string('jabatan')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('foto_profil')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
PHP

# ── 2. MASTER KECAMATAN ─────────────────────────────────────────
F=$(mig "create_master_kecamatan_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "master_kecamatan"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->string('kode_kecamatan', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('master_kecamatan'); }
};
PHP

# ── 3. MASTER KELURAHAN ─────────────────────────────────────────
F=$(mig "create_master_kelurahan_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "master_kelurahan"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_kelurahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('master_kecamatan')->cascadeOnDelete();
            $table->string('nama_kelurahan');
            $table->string('kode_kelurahan', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('master_kelurahan'); }
};
PHP

# ── 4. PELAPOR ──────────────────────────────────────────────────
F=$(mig "create_pelapor_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "pelapor"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pelapor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('email')->nullable();
            $table->foreignId('kecamatan_id')->nullable()->constrained('master_kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('master_kelurahan')->nullOnDelete();
            $table->boolean('anonim')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pelapor'); }
};
PHP

# ── 5. TERLAPOR ─────────────────────────────────────────────────
F=$(mig "create_terlapor_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "terlapor"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('terlapor', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['perusahaan', 'individu', 'instansi'])->default('perusahaan');
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('jenis_usaha')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('terlapor'); }
};
PHP

# ── 6. PENGADUAN ────────────────────────────────────────────────
F=$(mig "create_pengaduan_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "pengaduan"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengaduan', 30)->unique();
            $table->date('tanggal_pengaduan');
            $table->enum('status', ['masuk', 'diproses', 'verifikasi', 'selesai', 'ditolak'])->default('masuk');
            $table->string('kategori'); // pencemaran_udara, pencemaran_air, limbah_b3, dll
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
    }
    public function down(): void { Schema::dropIfExists('pengaduan'); }
};
PHP

# ── 7. VERIFIKASI LAPANGAN ──────────────────────────────────────
F=$(mig "create_verifikasi_lapangan_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "verifikasi_lapangan"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verifikasi_lapangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained('pengaduan')->cascadeOnDelete();
            $table->date('tanggal_verifikasi');
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('informasi_administrasi')->nullable(); // Section C BA
            $table->text('fakta_temuan')->nullable();           // Section D BA
            $table->text('saran_tindak_lanjut')->nullable();    // Section E BA
            $table->date('tenggat_tindak_lanjut')->nullable();  // +14 hari
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('verifikasi_lapangan'); }
};
PHP

# ── 8. TIM VERIFIKATOR ──────────────────────────────────────────
F=$(mig "create_tim_verifikator_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "tim_verifikator"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tim_verifikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nip', 30)->nullable();
            $table->string('pangkat')->nullable();
            $table->string('jabatan')->nullable();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tim_verifikator'); }
};
PHP

# ── 9. PENANGGUNG JAWAB USAHA ───────────────────────────────────
F=$(mig "create_penanggung_jawab_usaha_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "penanggung_jawab_usaha"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penanggung_jawab_usaha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama_pj');
            $table->string('jabatan_pj')->nullable();
            $table->string('nama_perusahaan');
            $table->text('alamat_perusahaan')->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->string('kbli', 20)->nullable();   // Kode KBLI
            $table->string('nib', 20)->nullable();    // Nomor Induk Berusaha
            $table->decimal('koordinat_lat', 10, 8)->nullable();
            $table->decimal('koordinat_lng', 11, 8)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('penanggung_jawab_usaha'); }
};
PHP

# ── 10. DOKUMENTASI FOTO ────────────────────────────────────────
F=$(mig "create_dokumentasi_foto_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "dokumentasi_foto"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dokumentasi_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('path_file');
            $table->text('keterangan')->nullable();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('dokumentasi_foto'); }
};
PHP

# ── 11. TANDA TANGAN ────────────────────────────────────────────
F=$(mig "create_tanda_tangan_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "tanda_tangan"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tanda_tangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->enum('tipe', ['verifikator', 'penanggung_jawab', 'saksi']);
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('nip', 30)->nullable();
            $table->text('data_ttd')->nullable(); // base64 SVG dari canvas
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tanda_tangan'); }
};
PHP

# ── 12. BERITA ACARA ────────────────────────────────────────────
F=$(mig "create_berita_acara_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "berita_acara"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verifikasi_id')->unique()->constrained('verifikasi_lapangan')->cascadeOnDelete();
            $table->string('nomor_ba', 80)->unique();       // auto-generate: BA/001/DLHK/VI/2026
            $table->date('tanggal_terbit');
            $table->string('qr_code_token', 64)->unique(); // token validasi QR
            $table->string('file_pdf_path')->nullable();
            $table->string('file_docx_path')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('berita_acara'); }
};
PHP

# ── 13. NOTIFIKASI ──────────────────────────────────────────────
F=$(mig "create_notifikasi_table")
[[ -n "$F" ]] && cat > "$F" << 'PHP' && ok "notifikasi"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe', 20)->default('info'); // info, warning, success, danger
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable(); // info model terkait
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('notifikasi'); }
};
PHP

# ════════════════════════════════════════════════════════════════
# BAGIAN 2: MODELS
# ════════════════════════════════════════════════════════════════
hdr "[2/5] Writing Models"

# ── User (modify existing) ─────────────────────────────────────
cat > "${MODEL}/User.php" << 'PHP' && ok "User"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'nip', 'jabatan', 'no_telp', 'foto_profil', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isPengawas(): bool  { return $this->role === 'pengawas'; }

    public function pengaduanDitugaskan() { return $this->hasMany(Pengaduan::class, 'assigned_to'); }
    public function verifikasiDibuat()    { return $this->hasMany(VerifikasiLapangan::class, 'created_by'); }
    public function notifikasi()          { return $this->hasMany(Notifikasi::class); }
}
PHP

# ── MasterKecamatan ────────────────────────────────────────────
cat > "${MODEL}/MasterKecamatan.php" << 'PHP' && ok "MasterKecamatan"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKecamatan extends Model
{
    protected $table    = 'master_kecamatan';
    protected $fillable = ['nama_kecamatan', 'kode_kecamatan', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function kelurahan()  { return $this->hasMany(MasterKelurahan::class, 'kecamatan_id'); }
    public function pelapor()    { return $this->hasMany(Pelapor::class, 'kecamatan_id'); }
    public function pengaduan()  { return $this->hasMany(Pengaduan::class, 'kecamatan_id'); }
}
PHP

# ── MasterKelurahan ────────────────────────────────────────────
cat > "${MODEL}/MasterKelurahan.php" << 'PHP' && ok "MasterKelurahan"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKelurahan extends Model
{
    protected $table    = 'master_kelurahan';
    protected $fillable = ['kecamatan_id', 'nama_kelurahan', 'kode_kelurahan', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function kecamatan() { return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id'); }
    public function pelapor()   { return $this->hasMany(Pelapor::class, 'kelurahan_id'); }
    public function pengaduan() { return $this->hasMany(Pengaduan::class, 'kelurahan_id'); }
}
PHP

# ── Pelapor ────────────────────────────────────────────────────
cat > "${MODEL}/Pelapor.php" << 'PHP' && ok "Pelapor"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelapor extends Model
{
    protected $table    = 'pelapor';
    protected $fillable = [
        'nama_pelapor', 'nik', 'alamat', 'no_telp', 'email',
        'kecamatan_id', 'kelurahan_id', 'anonim',
    ];
    protected $casts = ['anonim' => 'boolean'];

    public function kecamatan()  { return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id'); }
    public function kelurahan()  { return $this->belongsTo(MasterKelurahan::class, 'kelurahan_id'); }
    public function pengaduan()  { return $this->hasMany(Pengaduan::class, 'pelapor_id'); }

    public function getNamaDisplayAttribute(): string
    {
        return $this->anonim ? 'Anonim' : $this->nama_pelapor;
    }
}
PHP

# ── Terlapor ───────────────────────────────────────────────────
cat > "${MODEL}/Terlapor.php" << 'PHP' && ok "Terlapor"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terlapor extends Model
{
    protected $table    = 'terlapor';
    protected $fillable = ['nama', 'jenis', 'alamat', 'no_telp', 'jenis_usaha'];

    public function pengaduan() { return $this->hasMany(Pengaduan::class, 'terlapor_id'); }
}
PHP

# ── Pengaduan ──────────────────────────────────────────────────
cat > "${MODEL}/Pengaduan.php" << 'PHP' && ok "Pengaduan"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pengaduan extends Model
{
    protected $table    = 'pengaduan';
    protected $fillable = [
        'nomor_pengaduan', 'tanggal_pengaduan', 'status', 'kategori',
        'uraian_pengaduan', 'lokasi_kejadian', 'kecamatan_id', 'kelurahan_id',
        'koordinat_lat', 'koordinat_lng', 'pelapor_id', 'terlapor_id',
        'assigned_to', 'catatan_admin', 'dokumen_pendukung',
    ];
    protected $casts = ['tanggal_pengaduan' => 'date'];

    // Status badge helper
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'masuk'      => 'secondary',
            'diproses'   => 'warning',
            'verifikasi' => 'info',
            'selesai'    => 'success',
            'ditolak'    => 'danger',
            default      => 'secondary',
        };
    }

    // Auto-generate nomor pengaduan: PGD/001/VI/2026
    public static function generateNomor(): string
    {
        $bulan    = strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMM'));
        $tahun    = now()->year;
        $urut     = static::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('PGD/%03d/%s/%d', $urut, $bulan, $tahun);
    }

    // Scopes
    public function scopeStatus(Builder $q, string $status): Builder { return $q->where('status', $status); }
    public function scopeTahun(Builder $q, int $tahun): Builder       { return $q->whereYear('tanggal_pengaduan', $tahun); }

    // Relationships
    public function pelapor()     { return $this->belongsTo(Pelapor::class); }
    public function terlapor()    { return $this->belongsTo(Terlapor::class); }
    public function assignedTo()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function kecamatan()   { return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id'); }
    public function kelurahan()   { return $this->belongsTo(MasterKelurahan::class, 'kelurahan_id'); }
    public function verifikasi()  { return $this->hasOne(VerifikasiLapangan::class, 'pengaduan_id'); }
}
PHP

# ── VerifikasiLapangan ─────────────────────────────────────────
cat > "${MODEL}/VerifikasiLapangan.php" << 'PHP' && ok "VerifikasiLapangan"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiLapangan extends Model
{
    protected $table    = 'verifikasi_lapangan';
    protected $fillable = [
        'pengaduan_id', 'tanggal_verifikasi', 'status',
        'informasi_administrasi', 'fakta_temuan', 'saran_tindak_lanjut',
        'tenggat_tindak_lanjut', 'created_by',
    ];
    protected $casts = [
        'tanggal_verifikasi'    => 'date',
        'tenggat_tindak_lanjut' => 'date',
    ];

    public function pengaduan()         { return $this->belongsTo(Pengaduan::class); }
    public function pembuat()           { return $this->belongsTo(User::class, 'created_by'); }
    public function timVerifikator()    { return $this->hasMany(TimVerifikator::class, 'verifikasi_id'); }
    public function penanggungJawab()   { return $this->hasOne(PenanggungJawabUsaha::class, 'verifikasi_id'); }
    public function dokumentasiFoto()   { return $this->hasMany(DokumentasiFoto::class, 'verifikasi_id')->orderBy('urutan'); }
    public function tandaTangan()       { return $this->hasMany(TandaTangan::class, 'verifikasi_id')->orderBy('urutan'); }
    public function beritaAcara()       { return $this->hasOne(BeritaAcara::class, 'verifikasi_id'); }
}
PHP

# ── TimVerifikator ─────────────────────────────────────────────
cat > "${MODEL}/TimVerifikator.php" << 'PHP' && ok "TimVerifikator"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimVerifikator extends Model
{
    protected $table    = 'tim_verifikator';
    protected $fillable = ['verifikasi_id', 'nama', 'nip', 'pangkat', 'jabatan', 'urutan'];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
PHP

# ── PenanggungJawabUsaha ───────────────────────────────────────
cat > "${MODEL}/PenanggungJawabUsaha.php" << 'PHP' && ok "PenanggungJawabUsaha"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenanggungJawabUsaha extends Model
{
    protected $table    = 'penanggung_jawab_usaha';
    protected $fillable = [
        'verifikasi_id', 'nama_pj', 'jabatan_pj', 'nama_perusahaan',
        'alamat_perusahaan', 'bidang_usaha', 'kbli', 'nib',
        'koordinat_lat', 'koordinat_lng', 'no_telp', 'email',
    ];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
PHP

# ── DokumentasiFoto ────────────────────────────────────────────
cat > "${MODEL}/DokumentasiFoto.php" << 'PHP' && ok "DokumentasiFoto"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumentasiFoto extends Model
{
    protected $table    = 'dokumentasi_foto';
    protected $fillable = ['verifikasi_id', 'nama_file', 'path_file', 'keterangan', 'urutan'];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }

    public function getUrlAttribute(): string { return asset('storage/' . $this->path_file); }
}
PHP

# ── TandaTangan ────────────────────────────────────────────────
cat > "${MODEL}/TandaTangan.php" << 'PHP' && ok "TandaTangan"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TandaTangan extends Model
{
    protected $table    = 'tanda_tangan';
    protected $fillable = ['verifikasi_id', 'tipe', 'nama', 'jabatan', 'nip', 'data_ttd', 'urutan'];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
PHP

# ── BeritaAcara ────────────────────────────────────────────────
cat > "${MODEL}/BeritaAcara.php" << 'PHP' && ok "BeritaAcara"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeritaAcara extends Model
{
    protected $table    = 'berita_acara';
    protected $fillable = [
        'verifikasi_id', 'nomor_ba', 'tanggal_terbit',
        'qr_code_token', 'file_pdf_path', 'file_docx_path',
        'status', 'created_by',
    ];
    protected $casts = ['tanggal_terbit' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->qr_code_token)) {
                $model->qr_code_token = Str::random(48);
            }
        });
    }

    // BA/001/DLHK-KAB.TNG/VI/2026
    public static function generateNomor(): string
    {
        $bulan = strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMM'));
        $tahun = now()->year;
        $urut  = static::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('BA/%03d/DLHK-KAB.TNG/%s/%d', $urut, $bulan, $tahun);
    }

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
    public function pembuat()    { return $this->belongsTo(User::class, 'created_by'); }
}
PHP

# ── Notifikasi ─────────────────────────────────────────────────
cat > "${MODEL}/Notifikasi.php" << 'PHP' && ok "Notifikasi"
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table    = 'notifikasi';
    protected $fillable = ['user_id', 'judul', 'pesan', 'tipe', 'is_read', 'data'];
    protected $casts    = ['is_read' => 'boolean', 'data' => 'array'];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeBelumDibaca($q) { return $q->where('is_read', false); }
}
PHP

# ════════════════════════════════════════════════════════════════
# BAGIAN 3: MIDDLEWARE
# ════════════════════════════════════════════════════════════════
hdr "[3/5] Writing Middleware"

cat > "${MW}/AdminMiddleware.php" << 'PHP' && ok "AdminMiddleware"
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Akses ditolak. Hanya Admin yang dapat mengakses halaman ini.');
        }
        return $next($request);
    }
}
PHP

cat > "${MW}/PengawasMiddleware.php" << 'PHP' && ok "PengawasMiddleware"
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PengawasMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'pengawas'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Akses ditolak.');
        }
        return $next($request);
    }
}
PHP

# ════════════════════════════════════════════════════════════════
# BAGIAN 4: ROUTES
# ════════════════════════════════════════════════════════════════
hdr "[4/5] Writing Routes"

cat > "${ROUTES}/web.php" << 'PHP' && ok "web.php"
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
require __DIR__.'/admin.php';

// ── Pengawas ──────────────────────────────────────────────────
require __DIR__.'/pengawas.php';
PHP

cat > "${ROUTES}/admin.php" << 'PHP' && ok "admin.php"
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengaduanController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Admin\BeritaAcaraController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MasterDataController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Pengaduan
        Route::resource('pengaduan', PengaduanController::class);
        Route::patch('pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.status');
        Route::patch('pengaduan/{pengaduan}/assign', [PengaduanController::class, 'assign'])->name('pengaduan.assign');

        // Verifikasi Lapangan
        Route::resource('verifikasi', VerifikasiController::class);
        Route::post('verifikasi/{verifikasi}/finalize', [VerifikasiController::class, 'finalize'])->name('verifikasi.finalize');

        // Berita Acara
        Route::resource('berita-acara', BeritaAcaraController::class)->except(['edit', 'update']);
        Route::get('berita-acara/{ba}/preview',      [BeritaAcaraController::class, 'preview'])->name('berita-acara.preview');
        Route::get('berita-acara/{ba}/download-pdf', [BeritaAcaraController::class, 'downloadPdf'])->name('berita-acara.pdf');
        Route::get('berita-acara/{ba}/download-doc', [BeritaAcaraController::class, 'downloadDoc'])->name('berita-acara.doc');
        Route::get('berita-acara/{ba}/finalize',     [BeritaAcaraController::class, 'finalize'])->name('berita-acara.finalize');

        // Laporan
        Route::get('laporan',               [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export-excel',  [LaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('laporan/export-pdf',    [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

        // Manajemen User
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle');

        // Master Data
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('kecamatan', MasterDataController::class . '@kecamatan');
            Route::resource('kelurahan', MasterDataController::class . '@kelurahan');
        });
        Route::resource('master/kecamatan', [MasterDataController::class, 'kecamatanIndex'])->names([
            'index'   => 'master.kecamatan.index',
            'store'   => 'master.kecamatan.store',
            'update'  => 'master.kecamatan.update',
            'destroy' => 'master.kecamatan.destroy',
        ]);

    });
PHP

cat > "${ROUTES}/pengawas.php" << 'PHP' && ok "pengawas.php"
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pengawas\DashboardController;
use App\Http\Controllers\Pengawas\TugasController;
use App\Http\Controllers\Pengawas\VerifikasiController;
use App\Http\Controllers\Pengawas\ProfilController;

Route::prefix('pengawas')
    ->name('pengawas.')
    ->middleware(['auth', 'pengawas'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Tugas yang di-assign ke pengawas ini
        Route::get('tugas',               [TugasController::class, 'index'])->name('tugas.index');
        Route::get('tugas/{pengaduan}',   [TugasController::class, 'show'])->name('tugas.show');

        // Form verifikasi lapangan
        Route::get('verifikasi/{pengaduan}/create',  [VerifikasiController::class, 'create'])->name('verifikasi.create');
        Route::post('verifikasi/{pengaduan}',         [VerifikasiController::class, 'store'])->name('verifikasi.store');
        Route::get('verifikasi/{verifikasi}/edit',    [VerifikasiController::class, 'edit'])->name('verifikasi.edit');
        Route::patch('verifikasi/{verifikasi}',       [VerifikasiController::class, 'update'])->name('verifikasi.update');

        // Upload foto
        Route::post('verifikasi/{verifikasi}/foto',         [VerifikasiController::class, 'uploadFoto'])->name('verifikasi.foto');
        Route::delete('verifikasi/{verifikasi}/foto/{foto}', [VerifikasiController::class, 'deleteFoto'])->name('verifikasi.foto.delete');

        // Profil
        Route::get('profil',          [ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('profil',        [ProfilController::class, 'update'])->name('profil.update');

    });
PHP

# ── Route QR Code Publik (tanpa auth) ─────────────────────────
cat >> "${ROUTES}/web.php" << 'PHP'

// ── Validasi QR Code BA (publik) ──────────────────────────────
Route::get('/verify/{token}', function ($token) {
    $ba = \App\Models\BeritaAcara::where('qr_code_token', $token)
            ->with('verifikasi.pengaduan.terlapor')
            ->firstOrFail();
    return view('public.verify-ba', compact('ba'));
})->name('ba.verify');
PHP
ok "QR verify route (ditambahkan ke web.php)"

# ════════════════════════════════════════════════════════════════
# BAGIAN 5: SEEDER
# ════════════════════════════════════════════════════════════════
hdr "[5/5] Writing Seeders"

cat > "src/database/seeders/UserSeeder.php" << 'PHP' && ok "UserSeeder"
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sipplh.go.id'],
            [
                'name'     => 'Administrator SIPPLH',
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
                'nip'      => '198001012010011001',
                'jabatan'  => 'Administrator Sistem',
                'is_active'=> true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pengawas@sipplh.go.id'],
            [
                'name'     => 'Pengawas Demo',
                'password' => Hash::make('Pengawas@1234'),
                'role'     => 'pengawas',
                'nip'      => '198501012012011001',
                'jabatan'  => 'Pengawas Lingkungan Hidup Muda',
                'is_active'=> true,
            ]
        );

        echo "  ✓ Akun admin    : admin@sipplh.go.id     / Admin@1234\n";
        echo "  ✓ Akun pengawas : pengawas@sipplh.go.id  / Pengawas@1234\n";
    }
}
PHP

cat > "src/database/seeders/DatabaseSeeder.php" << 'PHP' && ok "DatabaseSeeder"
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
PHP

# ════════════════════════════════════════════════════════════════
# DAFTAR STUB CONTROLLER (create empty)
# ════════════════════════════════════════════════════════════════
hdr "Creating Controller Stubs"

CTRL="src/app/Http/Controllers"

write_ctrl() {
  local ns="$1" cls="$2" file="$3"
  cat > "$file" << PHPEOF
<?php
namespace App\Http\Controllers\\${ns};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ${cls} extends Controller
{
    // TODO: implement
}
PHPEOF
  ok "${cls}"
}

write_ctrl "Admin"    "DashboardController"  "${CTRL}/Admin/DashboardController.php"
write_ctrl "Admin"    "PengaduanController"  "${CTRL}/Admin/PengaduanController.php"
write_ctrl "Admin"    "VerifikasiController" "${CTRL}/Admin/VerifikasiController.php"
write_ctrl "Admin"    "BeritaAcaraController" "${CTRL}/Admin/BeritaAcaraController.php"
write_ctrl "Admin"    "LaporanController"    "${CTRL}/Admin/LaporanController.php"
write_ctrl "Admin"    "UserController"       "${CTRL}/Admin/UserController.php"
write_ctrl "Admin"    "MasterDataController" "${CTRL}/Admin/MasterDataController.php"
write_ctrl "Pengawas" "DashboardController"  "${CTRL}/Pengawas/DashboardController.php"
write_ctrl "Pengawas" "TugasController"      "${CTRL}/Pengawas/TugasController.php"
write_ctrl "Pengawas" "VerifikasiController" "${CTRL}/Pengawas/VerifikasiController.php"
write_ctrl "Pengawas" "ProfilController"     "${CTRL}/Pengawas/ProfilController.php"

# AuthController
cat > "${CTRL}/Auth/AuthController.php" << 'PHP' && ok "AuthController"
<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        if (!auth()->user()->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi Administrator.']);
        }

        $request->session()->regenerate();

        return match (auth()->user()->role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'pengawas' => redirect()->route('pengawas.dashboard'),
            default    => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
PHP

# ════════════════════════════════════════════════════════════════
# REGISTER MIDDLEWARE DI bootstrap/app.php
# ════════════════════════════════════════════════════════════════
hdr "Registering Middleware Aliases"

BOOT="src/bootstrap/app.php"
if grep -q "AdminMiddleware" "$BOOT" 2>/dev/null; then
  ok "Middleware sudah terdaftar (skip)"
else
  # Inject middleware aliases sebelum closing });
  python3 - "$BOOT" << 'PYEOF'
import sys, re

path = sys.argv[1]
with open(path, 'r') as f:
    content = f.read()

inject = """
    ->withMiddleware(function (\\Illuminate\\Foundation\\Configuration\\Middleware $middleware) {
        $middleware->alias([
            'admin'    => \\App\\Http\\Middleware\\AdminMiddleware::class,
            'pengawas' => \\App\\Http\\Middleware\\PengawasMiddleware::class,
        ]);
    })"""

# Insert before the last ->create(); or ->run();
content = re.sub(
    r'(->withRouting\([^;]+\))',
    r'\1' + inject,
    content,
    count=1
)

with open(path, 'w') as f:
    f.write(content)

print("  ✅  bootstrap/app.php updated")
PYEOF
fi

# ════════════════════════════════════════════════════════════════
# JALANKAN MIGRATE & SEED
# ════════════════════════════════════════════════════════════════
hdr "Running Migrations & Seeder"

echo "  ▶  php artisan migrate ..."
docker compose exec -T app php artisan migrate --force
echo ""
echo "  ▶  php artisan db:seed ..."
docker compose exec -T app php artisan db:seed --force

# ════════════════════════════════════════════════════════════════
# STORAGE LINK
# ════════════════════════════════════════════════════════════════
echo ""
echo "━━━ Storage Link ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
docker compose exec -T app php artisan storage:link 2>/dev/null || true
ok "storage:link"

# ════════════════════════════════════════════════════════════════
# DONE
# ════════════════════════════════════════════════════════════════
echo ""
echo "╔═════════════════════════════════════════════════════════╗"
echo "║   ✅  Setup selesai!                                    ║"
echo "╠═════════════════════════════════════════════════════════╣"
echo "║   Admin    : admin@sipplh.go.id    / Admin@1234         ║"
echo "║   Pengawas : pengawas@sipplh.go.id / Pengawas@1234      ║"
echo "╠═════════════════════════════════════════════════════════╣"
echo "║   Langkah berikutnya: build UI admin panel (layout,     ║"
echo "║   sidebar, dashboard) — konfirmasi ke Claude!           ║"
echo "╚═════════════════════════════════════════════════════════╝"
echo ""
