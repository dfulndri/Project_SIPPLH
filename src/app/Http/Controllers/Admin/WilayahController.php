<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKecamatan;
use App\Models\MasterKelurahan;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $kecamatans = MasterKecamatan::withCount('kelurahan')->orderBy('nama_kecamatan')->get();
        $kelurahans = MasterKelurahan::with('kecamatan')->orderBy('nama_kelurahan')->paginate(20, ['*'], 'kel_page');
        return view('admin.wilayah.index', compact('kecamatans', 'kelurahans'));
    }

    // ── Kecamatan CRUD ───────────────────────────────────────────
    public function storeKecamatan(Request $request)
    {
        $request->validate(['nama_kecamatan' => ['required', 'string', 'max:100', 'unique:master_kecamatan']]);
        MasterKecamatan::create($request->only('nama_kecamatan', 'kode_kecamatan'));
        return back()->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function updateKecamatan(Request $request, MasterKecamatan $kecamatan)
    {
        $request->validate(['nama_kecamatan' => ['required', 'string', 'max:100']]);
        $kecamatan->update($request->only('nama_kecamatan', 'kode_kecamatan', 'is_active'));
        return back()->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroyKecamatan(MasterKecamatan $kecamatan)
    {
        $kecamatan->delete();
        return back()->with('success', 'Kecamatan berhasil dihapus.');
    }

    // ── Kelurahan CRUD ───────────────────────────────────────────
    public function storeKelurahan(Request $request)
    {
        $request->validate([
            'kecamatan_id'   => ['required', 'exists:master_kecamatan,id'],
            'nama_kelurahan' => ['required', 'string', 'max:100'],
        ]);
        MasterKelurahan::create($request->only('kecamatan_id', 'nama_kelurahan', 'kode_kelurahan'));
        return back()->with('success', 'Kelurahan berhasil ditambahkan.');
    }

    public function updateKelurahan(Request $request, MasterKelurahan $kelurahan)
    {
        $request->validate(['nama_kelurahan' => ['required', 'string', 'max:100']]);
        $kelurahan->update($request->only('kecamatan_id', 'nama_kelurahan', 'kode_kelurahan', 'is_active'));
        return back()->with('success', 'Kelurahan berhasil diperbarui.');
    }

    public function destroyKelurahan(MasterKelurahan $kelurahan)
    {
        $kelurahan->delete();
        return back()->with('success', 'Kelurahan berhasil dihapus.');
    }
}
