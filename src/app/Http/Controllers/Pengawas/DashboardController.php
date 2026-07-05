<?php
namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\VerifikasiLapangan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $uid = Auth::id();

        $stats = [
            'total'      => Pengaduan::where('assigned_to', $uid)->count(),
            'pending'    => Pengaduan::where('assigned_to', $uid)->whereIn('status', ['didisposisikan', 'verifikasi_lapangan'])->count(),
            'verifikasi' => VerifikasiLapangan::where('created_by', $uid)->count(),
            'selesai'    => Pengaduan::where('assigned_to', $uid)->where('status', 'selesai')->count(),
        ];

        $tugasTerbaru = Pengaduan::where('assigned_to', $uid)
            ->with(['terlapor', 'kecamatan', 'disposisi'])
            ->latest()->take(5)->get();

        $verifikasiSaya = VerifikasiLapangan::where('created_by', $uid)
            ->with('pengaduan.terlapor')
            ->latest()->take(3)->get();

        // Map data for pengawas
        $mapData = Pengaduan::where('assigned_to', $uid)
            ->whereNotNull('koordinat_lat')->whereNotNull('koordinat_lng')
            ->with(['terlapor'])
            ->get()->map(fn($p) => [
                'lat'      => $p->koordinat_lat,
                'lng'      => $p->koordinat_lng,
                'nomor'    => $p->nomor_pengaduan,
                'terlapor' => $p->terlapor?->nama ?? '—',
                'status'   => $p->status_label,
                'status_key' => $p->status,
            ])->values()->toArray();

        return view('pengawas.dashboard.index', compact('stats', 'tugasTerbaru', 'verifikasiSaya', 'mapData'));
    }
}
