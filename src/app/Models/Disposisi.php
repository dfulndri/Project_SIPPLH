<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $table    = 'disposisi';
    protected $fillable = [
        'pengaduan_id', 'pengawas_id', 'jadwal_verifikasi', 'catatan', 'created_by',
    ];
    protected $casts = ['jadwal_verifikasi' => 'date'];

    public function pengaduan()  { return $this->belongsTo(Pengaduan::class); }
    public function pengawas()   { return $this->belongsTo(User::class, 'pengawas_id'); }
    public function pembuat()    { return $this->belongsTo(User::class, 'created_by'); }
}
