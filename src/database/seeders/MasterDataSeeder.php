<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterKecamatan;
use App\Models\MasterKelurahan;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Balaraja'       => ['Gembong','Sentul','Saga','Talagasari','Tobat','Cangkudu','Pasir Bolang','Sukamurni'],
            'Cikupa'         => ['Cikupa','Bitung Jaya','Pasir Jaya','Talaga','Pasir Gadung','Sukamulya','Budimulya','Dukuh','Cibadak','Bojong','Sukanegara'],
            'Cisauk'         => ['Cisauk','Cibogo','Suradita','Sampora','Dangdang'],
            'Cisoka'         => ['Cisoka','Selapajang','Carenang','Cisereh','Bojong Loa','Jeungjing','Pasanggrahan'],
            'Curug'          => ['Curug','Curug Wetan','Binong','Kadu','Cukang Galih','Sukabakti','Serdang Wetan','Serdang Kulon'],
            'Gunung Kaler'   => ['Gunung Kaler','Rancagong','Tamiang','Ketos','Sukatani','Sidoko','Kandawati','Mekarsari'],
            'Jambe'          => ['Jambe','Taban','Tipar Raya','Cibeuteung Udik','Cibeuteung Muara','Pasir Barat','Pasir Nangka'],
            'Jayanti'        => ['Jayanti','Cikande','Pasir Gintung','Pabuaran','Dangdeur','Parigi','Saga'],
            'Kelapa Dua'     => ['Kelapa Dua','Bencongan','Bencongan Indah','Pakulonan'],
            'Kemiri'         => ['Kemiri','Patra Manggala','Lontar','Rancaiyuh','Klebet','Muncung','Jenggot'],
            'Kosambi'        => ['Kosambi Barat','Kosambi Timur','Cikohod','Dadap','Rawa Burung','Salembaran Jati','Salembaran Jaya','Belimbing'],
            'Kronjo'         => ['Kronjo','Cirumpak','Pagedangan Ilir','Pagedangan Udik','Muncung','Blukbuk','Tobat','Bakung'],
            'Kresek'         => ['Kresek','Rancailat','Talok','Renged','Patrasana','Pasirampo','Jengkol','Kemuning'],
            'Legok'          => ['Legok','Babakan','Serdang Wetan','Caringin','Palasari','Rancagong','Kamuning'],
            'Mauk'           => ['Mauk Barat','Mauk Timur','Tanjung Anom','Sasak','Tegal Kunir Lor','Tegal Kunir Kidul','Banyu Asih'],
            'Makar Baru'     => ['Mekar Baru','Koper','Cibetok','Jatimulya','Cijeruk','Jenggot'],
            'Pagedangan'     => ['Pagedangan','Cihuni','Cijantra','Medang','Serdang Kulon','Jatake','Situgadung'],
            'Pakuhaji'       => ['Pakuhaji','Bonisari','Gempol Sari','Kalibaru','Kohod','Laksana','Muncung','Sukawali','Surya Bahari','Tanjung Burung'],
            'Panongan'       => ['Panongan','Ciakar','Mekar Bakti','Peusar','Sindangjaya','Serdang Wetan'],
            'Pasar Kemis'    => ['Pasar Kemis','Kuta Baru','Kuta Jaya','Gelam Jaya','Pangadegan','Sukamantri'],
            'Rajeg'          => ['Rajeg','Rajeg Mulya','Sukatani','Tanah Merah','Mekarsari','Ranjeng','Tanjakan','Tanjakan Mekar'],
            'Sepatan'        => ['Sepatan','Pondok Jaya','Kayu Bongkok','Karet','Mekar Kondang','Pisangan Jaya','Tanah Tinggi'],
            'Sepatan Timur'  => ['Bakung','Gempol Jaya','Jatiwaringin','Kedaung Barat','Lebak Wangi','Sangiang'],
            'Sindang Jaya'   => ['Sindang Jaya','Badak Anom','Pasir Muncang','Saga','Sukasari'],
            'Solear'         => ['Solear','Cikaret','Cikareo','Munjul','Pasir Nangka'],
            'Sukadiri'       => ['Sukadiri','Karang Serang','Pekayon','Rawa Kidang','Gempol Karya','Buniayu'],
            'Sukamulya'      => ['Sukamulya','Bunar','Kubang','Talaga Sari','Tegal Kunir'],
            'Tigaraksa'      => ['Tigaraksa','Pematang','Pasir Bolang','Sodong','Cileles','Jambu Karya','Kadu Agung','Pete','Cisereh'],
            'Teluknaga'      => ['Teluknaga','Bojong Renged','Kampung Besar','Kebon','Lemo','Pangkalan','Tanjung Pasir','Tegal Angus'],
        ];

        $this->command->info("Seeding 29 Kecamatan Kabupaten Tangerang...");

        foreach ($data as $nama => $kelurahans) {
            $kec = MasterKecamatan::updateOrCreate(
                ['nama_kecamatan' => $nama],
                ['is_active' => true]
            );
            foreach ($kelurahans as $kel) {
                MasterKelurahan::updateOrCreate(
                    ['kecamatan_id' => $kec->id, 'nama_kelurahan' => $kel],
                    ['is_active' => true]
                );
            }
            $this->command->line("  ✓ {$nama} (" . count($kelurahans) . " kelurahan)");
        }

        $this->command->info("✅ Total: " . MasterKecamatan::count() . " kecamatan, " . MasterKelurahan::count() . " kelurahan");
    }
}