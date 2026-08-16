<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terlapor extends Model
{
    protected $table    = 'terlapor';
    protected $fillable = [
        'nama',
        'jenis_terlapor',
        'alamat',
        'no_telp',
        'jenis_usaha',
        'nama_perusahaan',
        'npwp',
        'nib',
        'bidang_usaha',
        'penanggung_jawab',
        'jabatan_pj',
    ];

    public static array $jenisList = [
        'perorangan'    => 'Perorangan',
        'lembaga'       => 'Lembaga / Organisasi',
        'badan_hukum'   => 'Badan Hukum / Perusahaan',
        'objek_lainnya' => 'Objek Lainnya',
    ];

    public function getJenisLabelAttribute(): string
    {
        return self::$jenisList[$this->jenis_terlapor] ?? ucfirst($this->jenis_terlapor);
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'terlapor_id');
    }
}
