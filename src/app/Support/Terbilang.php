<?php

namespace App\Support;

class Terbilang
{
    protected static array $satuan = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas',
    ];

    /**
     * Ubah angka (dipakai untuk tanggal, 1-31) jadi kata dalam Bahasa Indonesia.
     * Contoh: 27 -> "Dua Puluh Tujuh", 10 -> "Sepuluh", 5 -> "Lima"
     */
    public static function angka(?int $n): string
    {
        if ($n === null) {
            return '-';
        }
        if ($n <= 11) {
            return self::$satuan[$n];
        }
        if ($n < 20) {
            return self::$satuan[$n - 10] . ' Belas';
        }
        if ($n < 100) {
            $puluh = intdiv($n, 10);
            $sisa  = $n % 10;
            $hasil = ($puluh === 1 ? '' : self::$satuan[$puluh] . ' ') . 'Puluh';
            if ($sisa > 0) {
                $hasil .= ' ' . self::$satuan[$sisa];
            }
            return $hasil;
        }
        return (string) $n; // fallback, tidak dipakai untuk tanggal (1-31)
    }
}
