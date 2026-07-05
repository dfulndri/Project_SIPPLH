<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelapor extends Model
{
    protected $table    = 'pelapor';
    protected $fillable = [
        'nama_pelapor', 'jenis_pelapor', 'nik', 'alamat', 'no_telp', 'email',
        'kecamatan_id', 'kelurahan_id', 'anonim',
        'nama_lembaga', 'jabatan_di_lembaga', 'npwp',
    ];
    protected $casts = ['anonim' => 'boolean'];

    public static array $jenisList = [
        'perorangan'  => 'Perorangan',
        'lembaga'     => 'Lembaga / Organisasi',
        'badan_hukum' => 'Badan Hukum / Perusahaan',
    ];

    public function kecamatan()  { return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id'); }
    public function kelurahan()  { return $this->belongsTo(MasterKelurahan::class, 'kelurahan_id'); }
    public function pengaduan()  { return $this->hasMany(Pengaduan::class, 'pelapor_id'); }

    public function getNamaDisplayAttribute(): string
    {
        return $this->anonim ? 'Anonim' : $this->nama_pelapor;
    }

    public function getJenisLabelAttribute(): string
    {
        return self::$jenisList[$this->jenis_pelapor] ?? 'Perorangan';
    }
}
