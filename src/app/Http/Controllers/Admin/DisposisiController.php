<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use App\Models\Disposisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Disposisi::with(['pengaduan.pelapor', 'pengaduan.terlapor', 'pengawas', 'pembuat'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('pengaduan', fn($q) => $q->where('nomor_pengaduan', 'like', "%{$s}%"));
        }

        $disposisis = $query->paginate(15)->withQueryString();
        return view('admin.disposisi.index', compact('disposisis'));
    }

    public function create(Request $request)
    {
        $pengaduan = null;
        if ($request->filled('pengaduan_id')) {
            $pengaduan = Pengaduan::with(['pelapor', 'terlapor'])->findOrFail($request->pengaduan_id);
        }

        $pengaduanList = Pengaduan::with('terlapor')
            ->whereIn('status', [Pengaduan::STATUS_PENGADUAN_BARU, Pengaduan::STATUS_MENUNGGU_DISPOSISI])
            ->latest()->get();

        $pengawas = User::where('role', 'pengawas')->where('is_active', true)->orderBy('name')->get();

        return view('admin.disposisi.create', compact('pengaduan', 'pengaduanList', 'pengawas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengaduan_id'       => ['required', 'exists:pengaduan,id'],
            'pengawas_id'        => ['required', 'exists:users,id'],
            'jadwal_verifikasi'  => ['required', 'date'],
            'catatan'            => ['nullable', 'string'],
        ]);

        $disposisi = Disposisi::create([
            'pengaduan_id'      => $request->pengaduan_id,
            'pengawas_id'       => $request->pengawas_id,
            'jadwal_verifikasi' => $request->jadwal_verifikasi,
            'catatan'           => $request->catatan,
            'created_by'        => Auth::id(),
        ]);

        // Update pengaduan status & assign
        $pengaduan = Pengaduan::findOrFail($request->pengaduan_id);
        $pengaduan->update([
            'status'      => Pengaduan::STATUS_DIDISPOSISIKAN,
            'assigned_to' => $request->pengawas_id,
        ]);

        return redirect()->route('admin.disposisi.index')
            ->with('success', 'Pengaduan berhasil didisposisikan ke pengawas.');
    }

    public function show(Disposisi $disposisi)
    {
        $disposisi->load(['pengaduan.pelapor', 'pengaduan.terlapor', 'pengaduan.kecamatan', 'pengawas', 'pembuat']);
        return view('admin.disposisi.show', compact('disposisi'));
    }
}
