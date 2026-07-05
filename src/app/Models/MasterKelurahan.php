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
