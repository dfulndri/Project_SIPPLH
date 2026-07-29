<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $query = VerifikasiLapangan::with(['pengaduan.terlapor', 'pembuat'])->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $verifikasis = $query->paginate(15)->withQueryString();
        return view('admin.verifikasi.index', compact('verifikasis'));
    }

    public function create(Request $request)
    {
        $pengaduan = null;
        if ($request->filled('pengaduan_id')) {
            $pengaduan = Pengaduan::with(['pelapor', 'terlapor', 'kecamatan'])->findOrFail($request->pengaduan_id);
        }

        $pengaduanList = Pengaduan::with('terlapor')
            ->whereIn('status', [Pengaduan::STATUS_DIDISPOSISIKAN, Pengaduan::STATUS_VERIFIKASI_LAPANGAN])
            ->whereDoesntHave('verifikasi')
            ->orderByDesc('created_at')->get();

        $pegawai = User::where('is_active', true)->whereIn('role', ['admin', 'pengawas'])->orderBy('name')->get();

        return view('admin.verifikasi.create', compact('pengaduan', 'pengaduanList', 'pegawai'));
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
            'tim.*.nama'             => ['required_with:tim', 'string', 'max:255'],
            'saksi.*.nama'           => ['nullable', 'string', 'max:255'],
            'foto.*'                 => ['nullable', 'file', 'image', 'max:5120'],
            'video'                  => ['nullable', 'file', 'mimes:mp4,avi,mov,wmv', 'max:51200'],
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

            // Tim Verifikator
            foreach (($request->tim ?? []) as $i => $t) {
                if (!empty($t['nama'])) {
                    TimVerifikator::create([
                        'verifikasi_id' => $v->id,
                        'nama'    => $t['nama'],
                        'nip'     => $t['nip']     ?? null,
                        'pangkat' => $t['pangkat'] ?? null,
                        'jabatan' => $t['jabatan'] ?? null,
                        'urutan'  => $i + 1,
                    ]);
                }
            }

            // Penanggung Jawab Usaha
            if ($request->filled('pj_nama_pj')) {
                PenanggungJawabUsaha::create([
                    'verifikasi_id'     => $v->id,
                    'nama_pj'           => $request->pj_nama_pj,
                    'jabatan_pj'        => $request->pj_jabatan_pj,
                    'nama_perusahaan'   => $request->pj_nama_perusahaan,
                    'alamat_perusahaan' => $request->pj_alamat,
                    'bidang_usaha'      => $request->pj_bidang_usaha,
                    'deskripsi_kegiatan' => $request->pj_deskripsi_kegiatan,
                    'kbli'              => $request->pj_kbli,
                    'nib'               => $request->pj_nib,
                    'status_permodalan' => $request->pj_status_permodalan,
                    'koordinat_lat'     => $request->pj_koordinat_lat,
                    'koordinat_lng'     => $request->pj_koordinat_lng,
                    'no_telp'           => $request->pj_no_telp,
                    'email'             => $request->pj_email,
                ]);
            }

            // Saksi
            foreach (($request->saksi ?? []) as $i => $s) {
                if (!empty($s['nama'])) {
                    Saksi::create([
                        'verifikasi_id' => $v->id,
                        'nama'    => $s['nama'],
                        'jabatan' => $s['jabatan'] ?? null,
                        'urutan'  => $i + 1,
                    ]);
                }
            }

            // Foto Dokumentasi
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

        return redirect()->route('admin.verifikasi.show', $newId)
            ->with('success', 'Verifikasi lapangan berhasil disimpan.');
    }

    public function show(VerifikasiLapangan $verifikasi)
    {
        $verifikasi->load([
            'pengaduan.pelapor',
            'pengaduan.terlapor',
            'pengaduan.kecamatan',
            'timVerifikator',
            'penanggungJawab',
            'dokumentasiFoto',
            'saksi',
            'tandaTangan',
            'beritaAcara',
            'pembuat',
        ]);
        return view('admin.verifikasi.show', compact('verifikasi'));
    }

    public function edit(VerifikasiLapangan $verifikasi)
    {
        if ($verifikasi->status === 'selesai') {
            return back()->with('error', 'Verifikasi yang sudah selesai tidak dapat diedit.');
        }
        $verifikasi->load(['timVerifikator', 'penanggungJawab', 'dokumentasiFoto', 'saksi', 'pengaduan.terlapor']);
        $pegawai = User::where('is_active', true)->whereIn('role', ['admin', 'pengawas'])->orderBy('name')->get();
        return view('admin.verifikasi.edit', compact('verifikasi', 'pegawai'));
    }

    public function update(Request $request, VerifikasiLapangan $verifikasi)
    {
        $request->validate([
            'tanggal_verifikasi'     => ['required', 'date'],
            'informasi_administrasi' => ['nullable', 'string'],
            'fakta_temuan'           => ['nullable', 'string'],
            'saran_tindak_lanjut'    => ['nullable', 'string'],
            'tenggat_tindak_lanjut'  => ['nullable', 'date'],
            'foto.*'                 => ['nullable', 'file', 'image', 'max:5120'],
        ]);

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

            // Reset & re-create tim
            $verifikasi->timVerifikator()->delete();
            foreach (($request->tim ?? []) as $i => $t) {
                if (!empty($t['nama'])) {
                    TimVerifikator::create([
                        'verifikasi_id' => $verifikasi->id,
                        'nama'    => $t['nama'],
                        'nip' => $t['nip'] ?? null,
                        'pangkat' => $t['pangkat'] ?? null,
                        'jabatan' => $t['jabatan'] ?? null,
                        'urutan'  => $i + 1,
                    ]);
                }
            }

            // Reset & re-create saksi
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

            // PJ upsert
            if ($request->filled('pj_nama_pj')) {
                PenanggungJawabUsaha::updateOrCreate(
                    ['verifikasi_id' => $verifikasi->id],
                    [
                        'nama_pj'           => $request->pj_nama_pj,
                        'jabatan_pj'        => $request->pj_jabatan_pj,
                        'nama_perusahaan'   => $request->pj_nama_perusahaan,
                        'alamat_perusahaan' => $request->pj_alamat,
                        'bidang_usaha'      => $request->pj_bidang_usaha,
                        'deskripsi_kegiatan' => $request->pj_deskripsi_kegiatan,
                        'kbli'              => $request->pj_kbli,
                        'nib'               => $request->pj_nib,
                        'status_permodalan' => $request->pj_status_permodalan,
                        'koordinat_lat'     => $request->pj_koordinat_lat,
                        'koordinat_lng'     => $request->pj_koordinat_lng,
                        'no_telp'           => $request->pj_no_telp,
                        'email'             => $request->pj_email,
                    ]
                );
            }

            // Add new photos
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

        return redirect()->route('admin.verifikasi.show', $verifikasi)
            ->with('success', 'Data verifikasi berhasil diperbarui.');
    }

    public function finalize(Request $request, VerifikasiLapangan $verifikasi)
    {
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

        return redirect()->route('admin.verifikasi.show', $verifikasi)
            ->with('success', 'Verifikasi selesai! Berita Acara otomatis dibuat.');
    }

    public function deleteFoto(VerifikasiLapangan $verifikasi, DokumentasiFoto $foto)
    {
        Storage::disk('public')->delete($foto->path_file);
        $foto->delete();
        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
