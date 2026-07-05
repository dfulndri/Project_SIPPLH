<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TindakLanjut;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        $query = TindakLanjut::with(['pengaduan.pelapor', 'pengaduan.terlapor', 'pembuat'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tindakLanjuts = $query->paginate(15)->withQueryString();
        return view('admin.tindak-lanjut.index', compact('tindakLanjuts'));
    }

    public function create(Request $request)
    {
        $pengaduan = null;
        if ($request->filled('pengaduan_id')) {
            $pengaduan = Pengaduan::with(['pelapor', 'terlapor'])->findOrFail($request->pengaduan_id);
        }

        $pengaduanList = Pengaduan::with('terlapor')
            ->whereIn('status', [Pengaduan::STATUS_VERIFIKASI_SELESAI, Pengaduan::STATUS_TINDAK_LANJUT])
            ->latest()->get();

        return view('admin.tindak-lanjut.create', compact('pengaduan', 'pengaduanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengaduan_id' => ['required', 'exists:pengaduan,id'],
            'tanggal'      => ['required', 'date'],
            'catatan'      => ['required', 'string', 'min:10'],
            'hasil'        => ['nullable', 'string'],
            'dokumen'      => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $docPath = $request->hasFile('dokumen')
            ? $request->file('dokumen')->store('tindak-lanjut/dokumen', 'public')
            : null;

        TindakLanjut::create([
            'pengaduan_id'  => $request->pengaduan_id,
            'tanggal'       => $request->tanggal,
            'catatan'       => $request->catatan,
            'hasil'         => $request->hasil,
            'status'        => 'proses',
            'dokumen_path'  => $docPath,
            'created_by'    => Auth::id(),
        ]);

        // Update pengaduan status
        Pengaduan::findOrFail($request->pengaduan_id)
            ->update(['status' => Pengaduan::STATUS_TINDAK_LANJUT]);

        return redirect()->route('admin.tindak-lanjut.index')
            ->with('success', 'Tindak lanjut berhasil ditambahkan.');
    }

    public function selesai(Request $request, TindakLanjut $tindakLanjut)
    {
        $request->validate(['hasil' => ['required', 'string']]);

        $tindakLanjut->update([
            'status' => 'selesai',
            'hasil'  => $request->hasil,
        ]);

        // Update pengaduan status
        $tindakLanjut->pengaduan->update(['status' => Pengaduan::STATUS_SELESAI]);

        return back()->with('success', 'Tindak lanjut diselesaikan.');
    }
}
