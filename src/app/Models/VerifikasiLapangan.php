<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiLapangan extends Model
{
    protected $table    = 'verifikasi_lapangan';
    protected $fillable = [
        'pengaduan_id', 'tanggal_verifikasi', 'jam_verifikasi', 'status',
        'koordinat_lat', 'koordinat_lng',
        'nama_penanggung_jawab', 'jabatan_pj',
        'informasi_administrasi', 'fakta_temuan', 'saran_tindak_lanjut',
        'tenggat_tindak_lanjut', 'video_path', 'catatan_tambahan', 'created_by',
    ];
    protected $casts = [
        'tanggal_verifikasi'    => 'date',
        'tenggat_tindak_lanjut' => 'date',
    ];

    public function pengaduan()       { return $this->belongsTo(Pengaduan::class); }
    public function pembuat()         { return $this->belongsTo(User::class, 'created_by'); }
    public function timVerifikator()  { return $this->hasMany(TimVerifikator::class, 'verifikasi_id'); }
    public function penanggungJawab() { return $this->hasOne(PenanggungJawabUsaha::class, 'verifikasi_id'); }
    public function dokumentasiFoto() { return $this->hasMany(DokumentasiFoto::class, 'verifikasi_id')->orderBy('urutan'); }
    public function tandaTangan()     { return $this->hasMany(TandaTangan::class, 'verifikasi_id')->orderBy('urutan'); }
    public function saksi()           { return $this->hasMany(Saksi::class, 'verifikasi_id')->orderBy('urutan'); }
    public function beritaAcara()     { return $this->hasOne(BeritaAcara::class, 'verifikasi_id'); }
}
