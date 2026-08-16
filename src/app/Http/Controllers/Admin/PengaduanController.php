<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengaduanRequest;
use App\Models\Pengaduan;
use App\Models\Pelapor;
use App\Models\Terlapor;
use App\Models\MasterKecamatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengaduanController extends Controller
{
    // ── Daftar Pengaduan ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Pengaduan::with(['pelapor', 'terlapor', 'kecamatan', 'assignedTo'])
            ->whereNotIn('status', [Pengaduan::STATUS_ARSIP])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nomor_pengaduan', 'like', "%{$s}%")
                    ->orWhereHas('pelapor',  fn($r) => $r->where('nama_pelapor', 'like', "%{$s}%"))
                    ->orWhereHas('terlapor', fn($r) => $r->where('nama', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('kecamatan_id')) $query->where('kecamatan_id', $request->kecamatan_id);

        $pengaduans = $query->paginate(15)->withQueryString();
        $kecamatans = MasterKecamatan::orderBy('nama_kecamatan')->get();

        return view('admin.pengaduan.index', [
            'pengaduans' => $pengaduans,
            'kecamatans' => $kecamatans,
        ]);
    }

    // ── Form Tambah ──────────────────────────────────────────────
    public function create()
    {
        return view('admin.pengaduan.create', [
            'kecamatans' => MasterKecamatan::where('is_active', true)->orderBy('nama_kecamatan')->get(),
            'pengawas'   => User::where('role', 'pengawas')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    // ── Simpan Pengaduan ─────────────────────────────────────────
    public function store(StorePengaduanRequest $request)
    {
        DB::transaction(function () use ($request) {
            $pelapor = Pelapor::create([
                'nama_pelapor'       => $request->nama_pelapor,
                'jenis_pelapor'      => $request->jenis_pelapor ?? 'perorangan',
                'nik'                => null, // field NIK dihapus dari form, sesuai Poin 2
                'alamat'             => $request->alamat_pelapor,
                'no_telp'            => $request->no_telp_pelapor,
                'email'              => $request->email_pelapor,
                'kecamatan_id'       => $request->kecamatan_pelapor,
                'kelurahan_id'       => $request->kelurahan_pelapor,
                'anonim'             => $request->boolean('anonim'),
                'nama_lembaga'       => $request->nama_lembaga,
                'jabatan_di_lembaga' => $request->jabatan_di_lembaga,
                'npwp'               => $request->npwp_pelapor,
                'nib'                => $request->nib_pelapor,
            ]);

            $terlapor = Terlapor::create([
                'nama'             => $request->nama_terlapor,
                'jenis_terlapor'   => $request->jenis_terlapor ?? 'perorangan',
                'alamat'           => $request->alamat_terlapor,
                'no_telp'          => $request->no_telp_terlapor,
                'jenis_usaha'      => $request->jenis_usaha,
                // Untuk Badan Hukum, cermin nilai identitas ke nama_perusahaan juga,
                // supaya tetap kompatibel dengan halaman Data Master > Data Perusahaan.
                'nama_perusahaan'  => $request->jenis_terlapor === 'badan_hukum' ? $request->nama_terlapor : null,
                'nib'              => $request->nib_terlapor,
                'npwp'             => $request->npwp_terlapor,
                'bidang_usaha'     => $request->bidang_usaha_terlapor,
                'penanggung_jawab' => $request->penanggung_jawab_terlapor,
                'jabatan_pj'       => $request->jabatan_pj_terlapor,
            ]);

            $docPath = $request->hasFile('dokumen_pendukung')
                ? $request->file('dokumen_pendukung')->store('pengaduan/dokumen', 'public')
                : null;

            Pengaduan::create([
                'nomor_pengaduan'   => Pengaduan::generateNomor(),
                'tanggal_pengaduan' => $request->tanggal_pengaduan,
                'sumber_laporan'    => $request->sumber_laporan ?? 'manual',
                'jenis_aduan'       => $request->jenis_aduan ?? [],
                'status'            => Pengaduan::STATUS_PENGADUAN_BARU,
                'uraian_pengaduan'  => $request->uraian_pengaduan,
                'lokasi_kejadian'   => $request->lokasi_kejadian,
                'kecamatan_id'      => $request->kecamatan_id,
                'kelurahan_id'      => $request->kelurahan_id,
                'koordinat_lat'     => $request->koordinat_lat,
                'koordinat_lng'     => $request->koordinat_lng,
                'pelapor_id'        => $pelapor->id,
                'terlapor_id'       => $terlapor->id,
                'dokumen_pendukung' => $docPath,
            ]);
        });

        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan berhasil ditambahkan ke sistem.');
    }

    // ── Detail ───────────────────────────────────────────────────
    public function show(Pengaduan $pengaduan)
    {
        $pengaduan->load([
            'pelapor.kecamatan',
            'pelapor.kelurahan',
            'terlapor',
            'kecamatan',
            'kelurahan',
            'assignedTo',
            'disposisi.pengawas',
            'disposisi.pembuat',
            'verifikasi.beritaAcara',
            'verifikasi.timVerifikator',
            'tindakLanjut',
        ]);

        $pengawas = User::where('role', 'pengawas')->where('is_active', true)->orderBy('name')->get();

        return view('admin.pengaduan.show', compact('pengaduan', 'pengawas'));
    }

    // ── Form Edit ────────────────────────────────────────────────
    public function edit(Pengaduan $pengaduan)
    {
        $pengaduan->load(['pelapor.kecamatan', 'pelapor.kelurahan', 'terlapor']);

        return view('admin.pengaduan.edit', [
            'pengaduan'  => $pengaduan,
            'kecamatans' => MasterKecamatan::where('is_active', true)->orderBy('nama_kecamatan')->get(),
        ]);
    }

    // ── Update ───────────────────────────────────────────────────
    public function update(StorePengaduanRequest $request, Pengaduan $pengaduan)
    {
        DB::transaction(function () use ($request, $pengaduan) {
            $pengaduan->pelapor->update([
                'nama_pelapor'       => $request->nama_pelapor,
                'jenis_pelapor'      => $request->jenis_pelapor ?? 'perorangan',
                'nik'                => null, // field NIK dihapus dari form, sesuai Poin 2
                'alamat'             => $request->alamat_pelapor,
                'no_telp'            => $request->no_telp_pelapor,
                'email'              => $request->email_pelapor,
                'kecamatan_id'       => $request->kecamatan_pelapor,
                'kelurahan_id'       => $request->kelurahan_pelapor,
                'anonim'             => $request->boolean('anonim'),
                'nama_lembaga'       => $request->nama_lembaga,
                'jabatan_di_lembaga' => $request->jabatan_di_lembaga,
                'npwp'               => $request->npwp_pelapor,
                'nib'                => $request->nib_pelapor,
            ]);

            $pengaduan->terlapor->update([
                'nama'             => $request->nama_terlapor,
                'jenis_terlapor'   => $request->jenis_terlapor ?? 'perorangan',
                'alamat'           => $request->alamat_terlapor,
                'no_telp'          => $request->no_telp_terlapor,
                'jenis_usaha'      => $request->jenis_usaha,
                'nama_perusahaan'  => $request->jenis_terlapor === 'badan_hukum' ? $request->nama_terlapor : null,
                'nib'              => $request->nib_terlapor,
                'npwp'             => $request->npwp_terlapor,
                'bidang_usaha'     => $request->bidang_usaha_terlapor,
                'penanggung_jawab' => $request->penanggung_jawab_terlapor,
                'jabatan_pj'       => $request->jabatan_pj_terlapor,
            ]);

            $docPath = $pengaduan->dokumen_pendukung;
            if ($request->hasFile('dokumen_pendukung')) {
                if ($docPath) Storage::disk('public')->delete($docPath);
                $docPath = $request->file('dokumen_pendukung')->store('pengaduan/dokumen', 'public');
            }

            $pengaduan->update([
                'tanggal_pengaduan' => $request->tanggal_pengaduan,
                'sumber_laporan'    => $request->sumber_laporan ?? 'manual',
                'jenis_aduan'       => $request->jenis_aduan ?? [],
                'uraian_pengaduan'  => $request->uraian_pengaduan,
                'lokasi_kejadian'   => $request->lokasi_kejadian,
                'kecamatan_id'      => $request->kecamatan_id,
                'kelurahan_id'      => $request->kelurahan_id,
                'koordinat_lat'     => $request->koordinat_lat,
                'koordinat_lng'     => $request->koordinat_lng,
                'dokumen_pendukung' => $docPath,
            ]);
        });

        return redirect()->route('admin.pengaduan.show', $pengaduan)
            ->with('success', 'Data pengaduan berhasil diperbarui.');
    }

    // ── Hapus ─────────────────────────────────────────────────────
    public function destroy(Pengaduan $pengaduan)
    {
        if ($pengaduan->dokumen_pendukung) {
            Storage::disk('public')->delete($pengaduan->dokumen_pendukung);
        }
        $pengaduan->delete();
        return redirect()->route('admin.pengaduan.index')
            ->with('success', 'Pengaduan ' . $pengaduan->nomor_pengaduan . ' berhasil dihapus.');
    }

    // ── Update Status ────────────────────────────────────────────
    public function updateStatus(Request $request, Pengaduan $pengaduan)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Pengaduan::$statusList))],
        ]);

        $pengaduan->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Status berhasil diubah menjadi ' . Pengaduan::$statusList[$request->status] . '.');
    }
}
