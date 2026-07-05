<?php
namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Auth;

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
            'verifikasi.pengaduan.pelapor', 'verifikasi.pengaduan.terlapor',
            'verifikasi.timVerifikator', 'verifikasi.penanggungJawab',
            'verifikasi.dokumentasiFoto', 'verifikasi.saksi',
        ]);

        return view('pengawas.berita-acara.show', compact('ba'));
    }
}
