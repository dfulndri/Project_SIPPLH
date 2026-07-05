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
