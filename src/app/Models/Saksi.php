<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saksi extends Model
{
    protected $table    = 'saksi';
    protected $fillable = ['verifikasi_id', 'nama', 'jabatan', 'urutan'];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
