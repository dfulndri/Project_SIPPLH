<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'nip', 'jabatan', 'no_telp', 'foto_profil', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isPengawas(): bool  { return $this->role === 'pengawas'; }

    public function pengaduanDitugaskan() { return $this->hasMany(Pengaduan::class, 'assigned_to'); }
    public function verifikasiDibuat()    { return $this->hasMany(VerifikasiLapangan::class, 'created_by'); }
    public function notifikasi()          { return $this->hasMany(Notifikasi::class); }
}
