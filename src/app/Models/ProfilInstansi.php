<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilInstansi extends Model
{
    protected $table    = 'profil_instansi';
    protected $fillable = [
        'nama_instansi', 'nama_kabupaten', 'nama_provinsi', 'alamat',
        'kode_pos', 'telepon', 'fax', 'email', 'website', 'logo_path',
        'nama_kepala_dinas', 'nip_kepala_dinas', 'jabatan_kepala_dinas',
        'pangkat_kepala_dinas', 'visi', 'misi', 'deskripsi',
        'kode_instansi', 'zona_waktu',
    ];

    /**
     * Get the singleton instance (first record or create default).
     */
    public static function getInstance(): self
    {
        return static::firstOrCreate([], [
            'nama_instansi'   => 'Dinas Lingkungan Hidup dan Kebersihan',
            'nama_kabupaten'  => 'Kabupaten Tangerang',
            'nama_provinsi'   => 'Banten',
            'alamat'          => 'Jl. Atik Soeardi Nomor 1, Gedung Lingkup PU LT Dasar-Puspem Tigaraksa, Tangerang, Banten, 15720',
            'telepon'         => '081188881398',
            'email'           => 'dlhk.kabtangerang1@gmail.com',
            'website'         => 'dlhk.tangerangkab.go.id',
        ]);
    }
}
