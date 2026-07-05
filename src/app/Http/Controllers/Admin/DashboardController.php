<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\VerifikasiLapangan;
use App\Models\BeritaAcara;
use App\Models\User;
use App\Models\MasterKecamatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunList = range(now()->year, max(2024, now()->year - 4));
        $tahun     = request('tahun', now()->year);

        $stats = [
            'total_pengaduan'  => Pengaduan::count(),
            'total_verifikasi' => VerifikasiLapangan::count(),
            'total_pengawas'   => User::where('role', 'pengawas')->where('is_active', true)->count(),
            'total_tahun'      => Pengaduan::whereYear('tanggal_pengaduan', now()->year)->count(),
        ];

        // Status counts
        $statusCounts = [];
        foreach (Pengaduan::$statusList as $key => $label) {
            $statusCounts[$key] = Pengaduan::where('status', $key)->count();
        }

        // Monthly trend
        $monthly = Pengaduan::selectRaw('MONTH(tanggal_pengaduan) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pengaduan', $tahun)
            ->groupBy('bulan')->pluck('total', 'bulan')->toArray();
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) $chartData[] = $monthly[$i] ?? 0;

        // Per jenis aduan - need to handle JSON field
        $allPengaduan = Pengaduan::pluck('jenis_aduan');
        $jenisCount = [];
        foreach ($allPengaduan as $jenis) {
            if (is_array($jenis)) {
                foreach ($jenis as $j) {
                    $jenisCount[$j] = ($jenisCount[$j] ?? 0) + 1;
                }
            }
        }
        arsort($jenisCount);
        $jenisLabels = array_map(
            fn($k) => Pengaduan::$jenisAduanList[$k] ?? ucwords(str_replace('_', ' ', $k)),
            array_keys($jenisCount)
        );

        // Per kecamatan
        $perKecamatan = Pengaduan::with('kecamatan')
            ->selectRaw('kecamatan_id, COUNT(*) as total')
            ->whereNotNull('kecamatan_id')->groupBy('kecamatan_id')
            ->orderByDesc('total')->take(10)->get();

        // Map data
        $mapData = Pengaduan::with(['pelapor', 'terlapor', 'kecamatan'])
            ->whereNotNull('koordinat_lat')
            ->whereNotNull('koordinat_lng')
            ->get()
            ->map(function ($p) {
                return [
                    'lat'      => $p->koordinat_lat,
                    'lng'      => $p->koordinat_lng,
                    'nomor'    => $p->nomor_pengaduan,
                    'pelapor'  => $p->pelapor?->nama_display ?? '—',
                    'terlapor' => $p->terlapor?->nama ?? '—',
                    'jenis'    => implode(', ', $p->jenis_aduan_labels),
                    'status'   => $p->status_label,
                    'status_key' => $p->status,
                    'tanggal'  => $p->tanggal_pengaduan->format('d M Y'),
                    'kec'      => $p->kecamatan?->nama_kecamatan,
                ];
            })->values()->toArray();

        $recentPengaduan = Pengaduan::with(['pelapor', 'terlapor', 'kecamatan'])->latest()->take(5)->get();

        // Filter data for charts
        $kecamatans = MasterKecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.dashboard.index', compact(
            'stats', 'statusCounts', 'chartData', 'tahun', 'tahunList',
            'jenisCount', 'jenisLabels', 'perKecamatan', 'mapData',
            'recentPengaduan', 'kecamatans'
        ));
    }

    public function chartData($tahun)
    {
        $monthly = Pengaduan::selectRaw('MONTH(tanggal_pengaduan) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pengaduan', (int)$tahun)
            ->groupBy('bulan')->pluck('total', 'bulan')->toArray();
        $data = [];
        for ($i = 1; $i <= 12; $i++) $data[] = $monthly[$i] ?? 0;
        return response()->json($data);
    }
}
