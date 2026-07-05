<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeritaAcara extends Model
{
    protected $table    = 'berita_acara';
    protected $fillable = [
        'verifikasi_id', 'nomor_ba', 'tanggal_terbit',
        'qr_code_token', 'file_pdf_path', 'file_docx_path',
        'status', 'created_by',
    ];
    protected $casts = ['tanggal_terbit' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->qr_code_token)) {
                $model->qr_code_token = Str::random(48);
            }
        });
    }

    // BA/001/DLHK-KAB.TNG/VI/2026
    public static function generateNomor(): string
    {
        $bulan = strtoupper(\Carbon\Carbon::now()->locale('id')->isoFormat('MMM'));
        $tahun = now()->year;
        $urut  = static::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('BA/%03d/DLHK-KAB.TNG/%s/%d', $urut, $bulan, $tahun);
    }

    public function verifikasi() { return $this->belongsTo(VerifikasiLapangan::class, 'verifikasi_id'); }
    public function pembuat()    { return $this->belongsTo(User::class, 'created_by'); }
}
