<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengaduan::with(['pelapor', 'terlapor', 'kecamatan'])
            ->whereIn('status', [Pengaduan::STATUS_SELESAI, Pengaduan::STATUS_ARSIP])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_pengaduan', 'like', "%{$s}%")
                  ->orWhereHas('pelapor', fn($r) => $r->where('nama_pelapor', 'like', "%{$s}%"))
                  ->orWhereHas('terlapor', fn($r) => $r->where('nama', 'like', "%{$s}%"));
            });
        }

        $pengaduans = $query->paginate(15)->withQueryString();
        return view('admin.arsip.index', compact('pengaduans'));
    }

    public function archive(Pengaduan $pengaduan)
    {
        $pengaduan->update(['status' => Pengaduan::STATUS_ARSIP]);
        return back()->with('success', 'Pengaduan ' . $pengaduan->nomor_pengaduan . ' dipindahkan ke arsip.');
    }
}
