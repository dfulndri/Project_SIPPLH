<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelapor;
use App\Models\MasterKecamatan;
use Illuminate\Http\Request;

class PelaporController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'perorangan');
        $query = Pelapor::where('jenis_pelapor', $jenis)
            ->with(['kecamatan', 'kelurahan'])
            ->withCount('pengaduan')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_pelapor', 'like', "%{$s}%")
                  ->orWhere('nik', 'like', "%{$s}%")
                  ->orWhere('no_telp', 'like', "%{$s}%");
            });
        }

        $pelapors   = $query->paginate(15)->withQueryString();
        $kecamatans = MasterKecamatan::where('is_active', true)->orderBy('nama_kecamatan')->get();

        return view('admin.master-data.pelapor.index', compact('pelapors', 'jenis', 'kecamatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelapor'  => ['required', 'string', 'max:255'],
            'jenis_pelapor' => ['required', 'in:perorangan,lembaga,badan_hukum'],
            'nik'           => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string'],
            'no_telp'       => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email'],
        ]);

        Pelapor::create($request->only([
            'nama_pelapor', 'jenis_pelapor', 'nik', 'alamat', 'no_telp', 'email',
            'kecamatan_id', 'kelurahan_id', 'nama_lembaga', 'jabatan_di_lembaga', 'npwp',
        ]));

        return back()->with('success', 'Data pelapor berhasil ditambahkan.');
    }

    public function update(Request $request, Pelapor $pelapor)
    {
        $request->validate([
            'nama_pelapor' => ['required', 'string', 'max:255'],
        ]);

        $pelapor->update($request->only([
            'nama_pelapor', 'jenis_pelapor', 'nik', 'alamat', 'no_telp', 'email',
            'kecamatan_id', 'kelurahan_id', 'nama_lembaga', 'jabatan_di_lembaga', 'npwp',
        ]));

        return back()->with('success', 'Data pelapor berhasil diperbarui.');
    }

    public function destroy(Pelapor $pelapor)
    {
        $pelapor->delete();
        return back()->with('success', 'Data pelapor berhasil dihapus.');
    }
}
