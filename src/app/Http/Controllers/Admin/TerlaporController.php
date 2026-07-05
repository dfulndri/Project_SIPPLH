<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Terlapor;
use Illuminate\Http\Request;

class TerlaporController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'perorangan');
        $query = Terlapor::where('jenis_terlapor', $jenis)
            ->withCount('pengaduan')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('nama_perusahaan', 'like', "%{$s}%");
            });
        }

        $terlapors = $query->paginate(15)->withQueryString();
        return view('admin.master-data.terlapor.index', compact('terlapors', 'jenis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'           => ['required', 'string', 'max:255'],
            'jenis_terlapor' => ['required', 'in:perorangan,badan_hukum,objek_lainnya'],
        ]);

        Terlapor::create($request->only([
            'nama', 'jenis_terlapor', 'alamat', 'no_telp', 'jenis_usaha',
            'nama_perusahaan', 'npwp', 'nib', 'bidang_usaha', 'penanggung_jawab', 'jabatan_pj',
        ]));

        return back()->with('success', 'Data terlapor berhasil ditambahkan.');
    }

    public function update(Request $request, Terlapor $terlapor)
    {
        $request->validate(['nama' => ['required', 'string', 'max:255']]);

        $terlapor->update($request->only([
            'nama', 'jenis_terlapor', 'alamat', 'no_telp', 'jenis_usaha',
            'nama_perusahaan', 'npwp', 'nib', 'bidang_usaha', 'penanggung_jawab', 'jabatan_pj',
        ]));

        return back()->with('success', 'Data terlapor berhasil diperbarui.');
    }

    public function destroy(Terlapor $terlapor)
    {
        $terlapor->delete();
        return back()->with('success', 'Data terlapor berhasil dihapus.');
    }
}
