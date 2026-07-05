<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjut extends Model
{
    protected $table    = 'tindak_lanjut';
    protected $fillable = [
        'pengaduan_id', 'tanggal', 'catatan', 'hasil', 'status', 'dokumen_path', 'created_by',
    ];
    protected $casts = ['tanggal' => 'date'];

    public function pengaduan() { return $this->belongsTo(Pengaduan::class); }
    public function pembuat()   { return $this->belongsTo(User::class, 'created_by'); }
}
