<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengaduan::where('assigned_to', Auth::id())
            ->with(['pelapor', 'terlapor', 'kecamatan', 'verifikasi', 'disposisi'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengaduans = $query->paginate(15)->withQueryString();
        return view('pengawas.tugas.index', compact('pengaduans'));
    }

    public function show(Pengaduan $pengaduan)
    {
        abort_if($pengaduan->assigned_to !== Auth::id(), 403, 'Anda tidak memiliki akses ke tugas ini.');

        $pengaduan->load([
            'pelapor.kecamatan',
            'pelapor.kelurahan',
            'terlapor',
            'kecamatan',
            'kelurahan',
            'disposisi.pembuat',
            'verifikasi.timVerifikator',
            'verifikasi.saksi',
            'verifikasi.penanggungJawab',
            'verifikasi.beritaAcara',
            'verifikasi.dokumentasiFoto',
            'tindakLanjut',
        ]);

        return view('pengawas.tugas.show', compact('pengaduan'));
    }
}
