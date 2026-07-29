<?php

namespace App\Support;

/**
 * Membuat versi kecil (thumbnail) dari sebuah gambar menggunakan GD,
 * agar dompdf tidak kehabisan memori saat me-render foto beresolusi tinggi.
 * Hasil di-cache di storage/app/public/berita-acara/pdf-cache/ dan dipakai ulang.
 *
 * Catatan: kelas ini berada di namespace App\Support, sehingga semua fungsi
 * global (GD, filesystem) dipanggil dengan prefix backslash "\" agar PHP
 * tidak mencarinya di dalam namespace ini.
 */
class ImageResizer
{
    public static function forPdf(string $sourcePath, int $maxDim = 900): ?string
    {
        // Butuh GD lengkap dengan dukungan JPEG. Jika tidak tersedia,
        // kembalikan file asli agar PDF tetap bisa dibuat (tanpa resize).
        if (!\is_file($sourcePath)) {
            return null;
        }
        if (!\function_exists('imagecreatetruecolor') || !\function_exists('imagejpeg')) {
            return $sourcePath;
        }

        $info = @\getimagesize($sourcePath);
        if ($info === false) {
            return $sourcePath;
        }

        $w = $info[0];
        $h = $info[1];
        $mime = $info['mime'] ?? '';

        // Kalau sudah kecil, pakai apa adanya
        if ($w <= $maxDim && $h <= $maxDim) {
            return $sourcePath;
        }

        // Direktori cache
        $cacheDir = \storage_path('app/public/berita-acara/pdf-cache');
        if (!\is_dir($cacheDir)) {
            @\mkdir($cacheDir, 0775, true);
        }

        // Nama cache berbasis hash (agar unik & bisa dipakai ulang)
        $key = \md5($sourcePath . '|' . \filemtime($sourcePath) . '|' . $maxDim);
        $dest = $cacheDir . '/' . $key . '.jpg';

        if (\is_file($dest)) {
            return $dest;
        }

        // Muat sumber sesuai tipe
        $src = null;
        switch ($mime) {
            case 'image/jpeg':
                $src = @\imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $src = @\imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $src = @\imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                if (\function_exists('imagecreatefromwebp')) {
                    $src = @\imagecreatefromwebp($sourcePath);
                }
                break;
        }

        if (!$src) {
            return $sourcePath;
        }

        // Hitung dimensi baru menjaga rasio
        $ratio = \min($maxDim / $w, $maxDim / $h);
        $nw = \max(1, (int) \round($w * $ratio));
        $nh = \max(1, (int) \round($h * $ratio));

        $dst = \imagecreatetruecolor($nw, $nh);
        // Latar putih (untuk PNG/GIF transparan agar tidak hitam di PDF)
        $white = \imagecolorallocate($dst, 255, 255, 255);
        \imagefilledrectangle($dst, 0, 0, $nw, $nh, $white);
        \imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        \imagejpeg($dst, $dest, 82);

        \imagedestroy($src);
        \imagedestroy($dst);

        return \is_file($dest) ? $dest : $sourcePath;
    }
}
