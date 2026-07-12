<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use App\Models\ProfilInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BeritaAcaraController extends Controller
{
    public function index(Request $request)
    {
        $query = BeritaAcara::with(['verifikasi.pengaduan.terlapor', 'pembuat'])->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $beritaAcaras = $query->paginate(15)->withQueryString();
        return view('admin.berita-acara.index', compact('beritaAcaras'));
    }

    public function show(BeritaAcara $ba)
    {
        $ba->load([
            'verifikasi.pengaduan.pelapor',
            'verifikasi.pengaduan.terlapor',
            'verifikasi.pengaduan.kecamatan',
            'verifikasi.pengaduan.kelurahan',
            'verifikasi.timVerifikator',
            'verifikasi.penanggungJawab',
            'verifikasi.dokumentasiFoto',
            'verifikasi.saksi',
            'pembuat',
        ]);
        return view('admin.berita-acara.show', compact('ba'));
    }

    public function finalize(Request $request, BeritaAcara $ba)
    {
        $ba->update(['status' => 'final']);
        return back()->with('success', 'Berita Acara ' . $ba->nomor_ba . ' telah difinalisasi.');
    }

    public function downloadPdf(BeritaAcara $ba)
    {
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
            ->setPaper('A4', 'portrait');

        $filename = 'BA_' . str_replace(['/', '\\'], '_', $ba->nomor_ba) . '.pdf';
        $path     = 'berita-acara/pdf/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $ba->update(['file_pdf_path' => $path]);

        return $pdf->download($filename);
    }
}
