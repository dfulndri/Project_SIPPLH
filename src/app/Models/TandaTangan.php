<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TandaTangan extends Model
{
    protected $table    = 'tanda_tangan';
    protected $fillable = ['verifikasi_id', 'tipe', 'nama', 'jabatan', 'nip', 'data_ttd', 'urutan'];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
