<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pengaduan extends Model
{
    protected $table    = 'pengaduan';
    protected $fillable = [
        'nomor_pengaduan', 'tanggal_pengaduan', 'sumber_laporan',
        'jenis_aduan', 'status', 'uraian_pengaduan', 'lokasi_kejadian',
        'kecamatan_id', 'kelurahan_id', 'koordinat_lat', 'koordinat_lng',
        'pelapor_id', 'terlapor_id', 'assigned_to', 'catatan_admin',
        'dokumen_pendukung',
    ];

    protected $casts = [
        'tanggal_pengaduan' => 'date',
        'jenis_aduan'       => 'array',
    ];

    // ── Status Constants ─────────────────────────────────────────
    const STATUS_PENGADUAN_BARU      = 'pengaduan_baru';
    const STATUS_MENUNGGU_DISPOSISI  = 'menunggu_disposisi';
    const STATUS_DIDISPOSISIKAN      = 'didisposisikan';
    const STATUS_VERIFIKASI_LAPANGAN = 'verifikasi_lapangan';
    const STATUS_VERIFIKASI_SELESAI  = 'verifikasi_selesai';
    const STATUS_TINDAK_LANJUT       = 'tindak_lanjut';
    const STATUS_SELESAI             = 'selesai';
    const STATUS_ARSIP               = 'arsip';

    public static array $statusList = [
        'pengaduan_baru'      => 'Pengaduan Baru',
        'menunggu_disposisi'  => 'Menunggu Disposisi',
        'didisposisikan'      => 'Didisposisikan',
        'verifikasi_lapangan' => 'Verifikasi Lapangan',
        'verifikasi_selesai'  => 'Verifikasi Selesai',
        'tindak_lanjut'       => 'Tindak Lanjut',
        'selesai'             => 'Selesai',
        'arsip'               => 'Arsip',
    ];

    public static array $jenisAduanList = [
        'pencemaran_air'          => 'Pencemaran Air',
        'pencemaran_udara'        => 'Pencemaran Udara',
        'pencemaran_limbah_b3'    => 'Pencemaran Limbah B3',
        'pencemaran_limbah_non_b3'=> 'Pencemaran Limbah Non B3',
        'pencemaran_sampah'       => 'Pencemaran Sampah',
        'pencemaran_lainnya'      => 'Pencemaran Lainnya',
    ];

    // ── Accessors ────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return self::$statusList[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pengaduan_baru'      => 'secondary',
            'menunggu_disposisi'  => 'warning',
            'didisposisikan'      => 'info',
            'verifikasi_lapangan' => 'primary',
            'verifikasi_selesai'  => 'success',
            'tindak_lanjut'       => 'dark',
            'selesai'             => 'success',
            'arsip'               => 'secondary',
            default               => 'secondary',
        };
    }

    public function getJenisAduanLabelsAttribute(): array
    {
        return array_map(
            fn($key) => self::$jenisAduanList[$key] ?? ucwords(str_replace('_', ' ', $key)),
            $this->jenis_aduan ?? []
        );
    }

    // ── Auto-generate nomor: PGD/001/VII/2026 ────────────────────
    public static function generateNomor(): string
    {
        $bulanRomawi = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];
        $bulan = $bulanRomawi[now()->month];
        $tahun = now()->year;
        $urut  = static::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('PGD/%03d/%s/%d', $urut, $bulan, $tahun);
    }

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    public function scopeTahun(Builder $q, int $tahun): Builder
    {
        return $q->whereYear('tanggal_pengaduan', $tahun);
    }

    // ── Relationships ────────────────────────────────────────────
    public function pelapor()     { return $this->belongsTo(Pelapor::class); }
    public function terlapor()    { return $this->belongsTo(Terlapor::class); }
    public function assignedTo()  { return $this->belongsTo(User::class, 'assigned_to'); }
    public function kecamatan()   { return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id'); }
    public function kelurahan()   { return $this->belongsTo(MasterKelurahan::class, 'kelurahan_id'); }
    public function verifikasi()  { return $this->hasOne(VerifikasiLapangan::class, 'pengaduan_id'); }
    public function disposisi()   { return $this->hasOne(Disposisi::class, 'pengaduan_id'); }
    public function tindakLanjut(){ return $this->hasMany(TindakLanjut::class, 'pengaduan_id'); }
}
