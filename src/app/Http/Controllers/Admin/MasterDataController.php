<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKecamatan;
use App\Models\MasterKelurahan;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $kecamatans  = MasterKecamatan::withCount('kelurahan')->orderBy('nama_kecamatan')->get();
        $kelurahan   = MasterKelurahan::with('kecamatan')->orderBy('nama_kelurahan');

        if ($request->filled('kec_filter')) {
            $kelurahan->where('kecamatan_id',$request->kec_filter);
        }

        $kelurahans = $kelurahan->paginate(25)->withQueryString();

        return view('admin.master-data.index', compact('kecamatans','kelurahans'));
    }

    // ── Kecamatan ────────────────────────────────────────────────
    public function storeKecamatan(Request $request)
    {
        $request->validate([
            'nama_kecamatan' => ['required','string','max:100','unique:master_kecamatan'],
            'kode_kecamatan' => ['nullable','string','max:20'],
        ]);

        MasterKecamatan::create($request->only('nama_kecamatan','kode_kecamatan'));

        return back()->with('success','Kecamatan berhasil ditambahkan.')->withFragment('kecamatan');
    }

    public function updateKecamatan(Request $request, MasterKecamatan $kecamatan)
    {
        $request->validate([
            'nama_kecamatan' => ['required','string','max:100',
                'unique:master_kecamatan,nama_kecamatan,'.$kecamatan->id],
            'kode_kecamatan' => ['nullable','string','max:20'],
        ]);

        $kecamatan->update($request->only('nama_kecamatan','kode_kecamatan'));

        return back()->with('success','Kecamatan berhasil diperbarui.')->withFragment('kecamatan');
    }

    public function destroyKecamatan(MasterKecamatan $kecamatan)
    {
        if ($kecamatan->kelurahan()->count() > 0) {
            return back()->with('error','Kecamatan masih memiliki kelurahan, hapus kelurahan terlebih dahulu.');
        }

        $kecamatan->delete();

        return back()->with('success','Kecamatan berhasil dihapus.')->withFragment('kecamatan');
    }

    // ── Kelurahan ────────────────────────────────────────────────
    public function storeKelurahan(Request $request)
    {
        $request->validate([
            'kecamatan_id'   => ['required','exists:master_kecamatan,id'],
            'nama_kelurahan' => ['required','string','max:100'],
            'kode_kelurahan' => ['nullable','string','max:20'],
        ]);

        MasterKelurahan::create($request->only('kecamatan_id','nama_kelurahan','kode_kelurahan'));

        return back()->with('success','Kelurahan berhasil ditambahkan.')->withFragment('kelurahan');
    }

    public function updateKelurahan(Request $request, MasterKelurahan $kelurahan)
    {
        $request->validate([
            'nama_kelurahan' => ['required','string','max:100'],
            'kode_kelurahan' => ['nullable','string','max:20'],
        ]);

        $kelurahan->update($request->only('nama_kelurahan','kode_kelurahan'));

        return back()->with('success','Kelurahan berhasil diperbarui.')->withFragment('kelurahan');
    }

    public function destroyKelurahan(MasterKelurahan $kelurahan)
    {
        $kelurahan->delete();

        return back()->with('success','Kelurahan berhasil dihapus.')->withFragment('kelurahan');
    }
}