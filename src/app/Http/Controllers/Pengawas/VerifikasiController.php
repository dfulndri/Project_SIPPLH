<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\VerifikasiLapangan;
use App\Models\Pengaduan;
use App\Models\TimVerifikator;
use App\Models\PenanggungJawabUsaha;
use App\Models\DokumentasiFoto;
use App\Models\Saksi;
use App\Models\BeritaAcara;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index()
    {
        $verifikasis = VerifikasiLapangan::where('created_by', Auth::id())
            ->with(['pengaduan.terlapor'])
            ->latest()->paginate(15);

        return view('pengawas.verifikasi.index', compact('verifikasis'));
    }

    public function create(Request $request)
    {
        $pengaduan = Pengaduan::with(['pelapor', 'terlapor', 'kecamatan'])
            ->findOrFail($request->pengaduan_id);

        abort_if($pengaduan->assigned_to !== Auth::id(), 403);

        $pegawai = User::where('is_active', true)
            ->whereIn('role', ['admin', 'pengawas'])->orderBy('name')->get();

        return view('pengawas.verifikasi.create', compact('pengaduan', 'pegawai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengaduan_id'           => ['required', 'exists:pengaduan,id'],
            'tanggal_verifikasi'     => ['required', 'date'],
            'jam_verifikasi'         => ['nullable', 'string'],
            'koordinat_lat'          => ['nullable', 'numeric'],
            'koordinat_lng'          => ['nullable', 'numeric'],
            'informasi_administrasi' => ['nullable', 'string'],
            'fakta_temuan'           => ['nullable', 'string'],
            'saran_tindak_lanjut'    => ['nullable', 'string'],
            'tenggat_tindak_lanjut'  => ['nullable', 'date'],
            'tim.*.nama'             => ['required_with:tim', 'string'],
            'saksi.*.nama'           => ['nullable', 'string'],
            'foto.*'                 => ['nullable', 'file', 'image', 'max:5120'],
            'video'                  => ['nullable', 'file', 'mimes:mp4,avi,mov,wmv', 'max:51200'],
            'pj_kbli_id' => 'nullable|exists:master_kbli,id',
        ]);

        $newId = null;

        DB::transaction(function () use ($request, &$newId) {
            $videoPath = $request->hasFile('video')
                ? $request->file('video')->store('verifikasi/video', 'public') : null;

            $v = VerifikasiLapangan::create([
                'pengaduan_id'           => $request->pengaduan_id,
                'tanggal_verifikasi'     => $request->tanggal_verifikasi,
                'jam_verifikasi'         => $request->jam_verifikasi,
                'status'                 => 'draft',
                'koordinat_lat'          => $request->koordinat_lat,
                'koordinat_lng'          => $request->koordinat_lng,
                'nama_penanggung_jawab'  => $request->nama_penanggung_jawab,
                'jabatan_pj'             => $request->jabatan_pj_verifikasi,
                'informasi_administrasi' => HtmlSanitizer::clean($request->informasi_administrasi),
                'fakta_temuan'           => HtmlSanitizer::clean($request->fakta_temuan),
                'saran_tindak_lanjut'    => HtmlSanitizer::clean($request->saran_tindak_lanjut),
                'tenggat_tindak_lanjut'  => $request->tenggat_tindak_lanjut,
                'video_path'             => $videoPath,
                'created_by'             => Auth::id(),
            ]);
            $newId = $v->id;

            Pengaduan::find($request->pengaduan_id)->update(['status' => Pengaduan::STATUS_VERIFIKASI_LAPANGAN]);

            foreach (($request->tim ?? []) as $i => $t) {
                if (!empty($t['nama'])) {
                    TimVerifikator::create([
                        'verifikasi_id' => $v->id,
                        'nama' => $t['nama'],
                        'nip' => $t['nip'] ?? null,
                        'pangkat' => $t['pangkat'] ?? null,
                        'jabatan' => $t['jabatan'] ?? null,
                        'urutan' => $i + 1,
                    ]);
                }
            }

            if ($request->filled('pj_nama_pj')) {
                PenanggungJawabUsaha::create([
                    'verifikasi_id'     => $v->id,
                    'nama_pj'           => $request->pj_nama_pj,
                    'jabatan_pj'        => $request->pj_jabatan_pj,
                    'nama_perusahaan'   => $request->pj_nama_perusahaan,
                    'alamat_perusahaan' => $request->pj_alamat,
                    'bidang_usaha'      => $request->pj_bidang_usaha,
                    'deskripsi_kegiatan' => $request->pj_deskripsi_kegiatan,
                    'kbli_id'           => $request->pj_kbli_id,
                    'nib'               => $request->pj_nib,
                    'status_permodalan' => $request->pj_status_permodalan,
                    'koordinat_lat'     => $request->pj_koordinat_lat,
                    'koordinat_lng'     => $request->pj_koordinat_lng,
                    'no_telp'           => $request->pj_no_telp,
                    'email'             => $request->pj_email,
                ]);
            }

            foreach (($request->saksi ?? []) as $i => $s) {
                if (!empty($s['nama'])) {
                    Saksi::create([
                        'verifikasi_id' => $v->id,
                        'nama' => $s['nama'],
                        'jabatan' => $s['jabatan'] ?? null,
                        'urutan' => $i + 1,
                    ]);
                }
            }

            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $i => $file) {
                    DokumentasiFoto::create([
                        'verifikasi_id' => $v->id,
                        'nama_file'     => $file->getClientOriginalName(),
                        'path_file'     => $file->store('verifikasi/foto', 'public'),
                        'keterangan'    => $request->foto_keterangan[$i] ?? null,
                        'urutan'        => $i + 1,
                    ]);
                }
            }
        });

        return redirect()->route('pengawas.tugas.show', $request->pengaduan_id)
            ->with('success', 'Verifikasi lapangan berhasil disimpan.');
    }

    public function edit(VerifikasiLapangan $verifikasi)
    {
        abort_if($verifikasi->created_by !== Auth::id(), 403);
        $verifikasi->load(['timVerifikator', 'penanggungJawab', 'dokumentasiFoto', 'saksi', 'pengaduan.terlapor']);
        $pegawai = User::where('is_active', true)->whereIn('role', ['admin', 'pengawas'])->orderBy('name')->get();
        return view('pengawas.verifikasi.edit', compact('verifikasi', 'pegawai'));
    }

    public function update(Request $request, VerifikasiLapangan $verifikasi)
    {
        abort_if($verifikasi->created_by !== Auth::id(), 403);

        DB::transaction(function () use ($request, $verifikasi) {
            $verifikasi->update([
                'tanggal_verifikasi'     => $request->tanggal_verifikasi,
                'jam_verifikasi'         => $request->jam_verifikasi,
                'koordinat_lat'          => $request->koordinat_lat,
                'koordinat_lng'          => $request->koordinat_lng,
                'nama_penanggung_jawab'  => $request->nama_penanggung_jawab,
                'jabatan_pj'             => $request->jabatan_pj_verifikasi,
                'informasi_administrasi' => HtmlSanitizer::clean($request->informasi_administrasi),
                'fakta_temuan'           => HtmlSanitizer::clean($request->fakta_temuan),
                'saran_tindak_lanjut'    => HtmlSanitizer::clean($request->saran_tindak_lanjut),
                'tenggat_tindak_lanjut'  => $request->tenggat_tindak_lanjut,
            ]);

            $verifikasi->timVerifikator()->delete();
            foreach (($request->tim ?? []) as $i => $t) {
                if (!empty($t['nama'])) {
                    TimVerifikator::create([
                        'verifikasi_id' => $verifikasi->id,
                        'nama' => $t['nama'],
                        'nip' => $t['nip'] ?? null,
                        'pangkat' => $t['pangkat'] ?? null,
                        'jabatan' => $t['jabatan'] ?? null,
                        'urutan' => $i + 1,
                    ]);
                }
            }

            $verifikasi->saksi()->delete();
            foreach (($request->saksi ?? []) as $i => $s) {
                if (!empty($s['nama'])) {
                    Saksi::create([
                        'verifikasi_id' => $verifikasi->id,
                        'nama' => $s['nama'],
                        'jabatan' => $s['jabatan'] ?? null,
                        'urutan' => $i + 1,
                    ]);
                }
            }

            if ($request->filled('pj_nama_pj') || $request->filled('pj_nama_perusahaan')) {
                PenanggungJawabUsaha::updateOrCreate(
                    ['verifikasi_id' => $verifikasi->id],
                    [
                        'nama_pj'            => $request->pj_nama_pj,
                        'jabatan_pj'         => $request->pj_jabatan_pj,
                        'nama_perusahaan'    => $request->pj_nama_perusahaan,
                        'alamat_perusahaan'  => $request->pj_alamat,
                        'bidang_usaha'       => $request->pj_bidang_usaha,
                        'deskripsi_kegiatan' => $request->pj_deskripsi_kegiatan,
                        'kbli_id'           => $request->pj_kbli_id,
                        'nib'                => $request->pj_nib,
                        'status_permodalan'  => $request->pj_status_permodalan,
                        'koordinat_lat'      => $request->pj_koordinat_lat,
                        'koordinat_lng'      => $request->pj_koordinat_lng,
                        'no_telp'            => $request->pj_no_telp,
                        'email'              => $request->pj_email,
                    ]
                );
            }

            if ($request->hasFile('foto')) {
                $base = $verifikasi->dokumentasiFoto()->count();
                foreach ($request->file('foto') as $i => $file) {
                    DokumentasiFoto::create([
                        'verifikasi_id' => $verifikasi->id,
                        'nama_file'     => $file->getClientOriginalName(),
                        'path_file'     => $file->store('verifikasi/foto', 'public'),
                        'keterangan'    => $request->foto_keterangan[$i] ?? null,
                        'urutan'        => $base + $i + 1,
                    ]);
                }
            }
        });

        return redirect()->route('pengawas.tugas.show', $verifikasi->pengaduan_id)
            ->with('success', 'Verifikasi berhasil diperbarui.');
    }

    public function finalize(Request $request, VerifikasiLapangan $verifikasi)
    {
        abort_if($verifikasi->created_by !== Auth::id(), 403);

        if ($verifikasi->timVerifikator->isEmpty()) {
            return back()->with('error', 'Tambahkan Tim Verifikator terlebih dahulu.');
        }

        DB::transaction(function () use ($verifikasi) {
            $verifikasi->update(['status' => 'selesai']);
            $verifikasi->pengaduan->update(['status' => Pengaduan::STATUS_VERIFIKASI_SELESAI]);

            if (!$verifikasi->beritaAcara) {
                BeritaAcara::create([
                    'verifikasi_id'  => $verifikasi->id,
                    'nomor_ba'       => BeritaAcara::generateNomor(),
                    'tanggal_terbit' => now()->toDateString(),
                    'status'         => 'draft',
                    'created_by'     => Auth::id(),
                ]);
            }
        });

        return redirect()->route('pengawas.berita-acara.show', $verifikasi->beritaAcara()->first())
            ->with('success', 'Verifikasi selesai! Berita Acara otomatis dibuat.');
    }

    public function deleteFoto(VerifikasiLapangan $verifikasi, DokumentasiFoto $foto)
    {
        abort_if($verifikasi->created_by !== Auth::id(), 403);
        Storage::disk('public')->delete($foto->path_file);
        $foto->delete();
        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
