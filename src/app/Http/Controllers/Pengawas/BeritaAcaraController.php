<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use App\Models\ProfilInstansi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        $beritaAcaras = BeritaAcara::with(['verifikasi.pengaduan.terlapor'])
            ->whereHas('verifikasi', fn($q) => $q->where('created_by', Auth::id()))
            ->latest()->paginate(15);

        return view('pengawas.berita-acara.index', compact('beritaAcaras'));
    }

    public function show(BeritaAcara $ba)
    {
        abort_if($ba->verifikasi?->created_by !== Auth::id(), 403);

        $ba->load([
            'verifikasi.pengaduan.pelapor',
            'verifikasi.pengaduan.terlapor',
            'verifikasi.timVerifikator',
            'verifikasi.penanggungJawab',
            'verifikasi.dokumentasiFoto',
            'verifikasi.saksi',
        ]);

        return view('pengawas.berita-acara.show', compact('ba'));
    }

    public function downloadPdf(BeritaAcara $ba)
    {
        abort_if($ba->verifikasi?->created_by !== Auth::id(), 403);

        // Jaring pengaman: rendering PDF dengan foto bisa boros memori
        @ini_set('memory_limit', '512M');

        $ba->load([
            'verifikasi.pengaduan.pelapor',
            'verifikasi.pengaduan.terlapor',
            'verifikasi.pengaduan.kecamatan',
            'verifikasi.timVerifikator',
            'verifikasi.penanggungJawab',
            'verifikasi.dokumentasiFoto',
            'verifikasi.saksi',
            'pembuat',
        ]);

        $profil = ProfilInstansi::getInstance();

        // Logo path
        $logoPath = null;
        if ($profil->logo_path) {
            $fullPath = public_path($profil->logo_path);
            if (!file_exists($fullPath)) {
                $fullPath = storage_path('app/public/' . $profil->logo_path);
            }
            if (file_exists($fullPath)) {
                $logoPath = $fullPath;
            }
        }

        // Generate QR Code
        $verifyUrl = route('ba.verify', $ba->qr_code_token);
        $qrCode = base64_encode(QrCode::format('svg')->size(120)->margin(0)->generate($verifyUrl));

        $pdf = Pdf::loadView('pdf.berita-acara', compact('ba', 'qrCode', 'profil', 'logoPath'))
            ->setPaper('A4', 'portrait')
            ->setOption('dpi', 96)
            ->setOption('isRemoteEnabled', false)
            ->setOption('isHtml5ParserEnabled', true);

        $filename = 'BA_' . str_replace(['/', '\\'], '_', $ba->nomor_ba) . '.pdf';
        $path     = 'berita-acara/pdf/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $ba->update(['file_pdf_path' => $path]);

        return $pdf->download($filename);
    }
}
