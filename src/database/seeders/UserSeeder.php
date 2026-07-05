<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sipplh.go.id'],
            [
                'name'     => 'Administrator SIPPLH',
                'password' => Hash::make('Admin@1234'),
                'role'     => 'admin',
                'nip'      => '198001012010011001',
                'jabatan'  => 'Administrator Sistem',
                'is_active'=> true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pengawas@sipplh.go.id'],
            [
                'name'     => 'Pengawas Demo',
                'password' => Hash::make('Pengawas@1234'),
                'role'     => 'pengawas',
                'nip'      => '198501012012011001',
                'jabatan'  => 'Pengawas Lingkungan Hidup Muda',
                'is_active'=> true,
            ]
        );

        echo "  ✓ Akun admin    : admin@sipplh.go.id     / Admin@1234\n";
        echo "  ✓ Akun pengawas : pengawas@sipplh.go.id  / Pengawas@1234\n";
    }
}
