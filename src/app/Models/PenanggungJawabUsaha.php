<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenanggungJawabUsaha extends Model
{
    protected $table    = 'penanggung_jawab_usaha';
    protected $fillable = [
        'verifikasi_id', 'nama_pj', 'jabatan_pj', 'nama_perusahaan',
        'alamat_perusahaan', 'bidang_usaha', 'deskripsi_kegiatan',
        'kbli', 'nib', 'status_permodalan',
        'koordinat_lat', 'koordinat_lng', 'no_telp', 'email',
    ];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
