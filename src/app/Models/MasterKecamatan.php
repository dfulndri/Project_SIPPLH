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
