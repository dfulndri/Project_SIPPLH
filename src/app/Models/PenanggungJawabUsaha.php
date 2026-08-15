<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenanggungJawabUsaha extends Model
{
    protected $table    = 'penanggung_jawab_usaha';
    protected $fillable = [
        'verifikasi_id',
        'nama_pj',
        'jabatan_pj',
        'nama_perusahaan',
        'alamat_perusahaan',
        'bidang_usaha',
        'deskripsi_kegiatan',
        'kbli',
        'kbli_id',
        'nib',
        'status_permodalan',
        'koordinat_lat',
        'koordinat_lng',
        'no_telp',
        'email',
    ];

    public function verifikasi()
    {
        return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id');
    }

    public function masterKbli()
    {
        return $this->belongsTo(MasterKbli::class, 'kbli_id');
    }

    /** Teks tampilan KBLI: pakai relasi kalau kbli_id terisi, fallback ke kolom teks lama. */
    public function getKbliDisplayAttribute(): ?string
    {
        if ($this->masterKbli) {
            return "{$this->masterKbli->kode_kbli} - {$this->masterKbli->judul}";
        }
        return $this->kbli;
    }
}
