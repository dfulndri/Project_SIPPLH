<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfilInstansi;

class ProfilInstansiSeeder extends Seeder
{
    public function run(): void
    {
        ProfilInstansi::updateOrCreate(['id' => 1], [
            'nama_instansi'       => 'Dinas Lingkungan Hidup dan Kebersihan',
            'nama_kabupaten'      => 'Kabupaten Tangerang',
            'nama_provinsi'       => 'Banten',
            'alamat'              => 'Jl. Atik Soeardi Nomor 1, Gedung Lingkup PU LT Dasar-Puspem Tigaraksa, Tangerang, Banten, 15720',
            'kode_pos'            => '15720',
            'telepon'             => '081188881398',
            'email'               => 'dlhk.kabtangerang1@gmail.com',
            'website'             => 'dlhk.tangerangkab.go.id',
            'logo_path'           => 'images/logo_kabupatentangerang_perda.png',
            'jabatan_kepala_dinas'=> 'Kepala Dinas',
        ]);

        $this->command->info('✅ Profil Instansi seeded.');
    }
}
