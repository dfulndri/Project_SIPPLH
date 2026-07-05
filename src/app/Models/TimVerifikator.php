<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimVerifikator extends Model
{
    protected $table    = 'tim_verifikator';
    protected $fillable = ['verifikasi_id', 'nama', 'nip', 'pangkat', 'jabatan', 'urutan'];

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
}
