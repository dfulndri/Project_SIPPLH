<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table    = 'notifikasi';
    protected $fillable = ['user_id', 'judul', 'pesan', 'tipe', 'is_read', 'data'];
    protected $casts    = ['is_read' => 'boolean', 'data' => 'array'];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeBelumDibaca($q) { return $q->where('is_read', false); }
}
