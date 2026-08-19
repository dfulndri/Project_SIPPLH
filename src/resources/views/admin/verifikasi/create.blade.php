@extends('layouts.admin')
@section('title', 'Buat Verifikasi Lapangan')
@section('breadcrumb', 'Buat Verifikasi')

@section('content')

    <div class="page-hd d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-ttl">Buat Verifikasi Lapangan</h1>
            <p class="page-stl">Isi formulir berikut sesuai hasil verifikasi di lapangan.</p>
        </div>
        <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" style="font-size:.85rem">
            <strong><i class="bi bi-exclamation-circle-fill me-1"></i> Periksa kembali isian:</strong>
            <ul class="mb-0 mt-1 ps-4">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.verifikasi.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ══ BAGIAN 0: PENGADUAN TERKAIT ════════════════════════════ --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        0</div>
                    <div>
                        <div class="cp-title">Pengaduan yang Diverifikasi</div>
                        <div class="cp-sub">Pilih pengaduan yang menjadi dasar verifikasi lapangan ini</div>
                    </div>
                </div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Pilih Pengaduan <span class="text-danger">*</span></label>
                        @if ($pengaduan)
                            {{-- Pre-selected dari halaman pengaduan --}}
                            <div class="d-flex align-items-center gap-2 p-2 rounded"
                                style="background:var(--mint-bg);border:1px solid var(--mint)">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <div>
                                    <code>{{ $pengaduan->nomor_pengaduan }}</code>
                                    <span class="ms-1" style="font-size:.82rem">— {{ $pengaduan->terlapor?->nama }}</span>
                                </div>
                            </div>
                            <input type="hidden" name="pengaduan_id" value="{{ $pengaduan->id }}">
                        @else
                            <select name="pengaduan_id" class="form-select @error('pengaduan_id') is-invalid @enderror">
                                <option value="">-- Pilih Pengaduan --</option>
                                @foreach ($pengaduanList as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old('pengaduan_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nomor_pengaduan }} — {{ $p->terlapor?->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($pengaduanList->isEmpty())
                                <div class="form-text text-warning">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Tidak ada pengaduan yang siap diverifikasi. Pastikan ada pengaduan berstatus "Diproses".
                                </div>
                            @endif
                            @error('pengaduan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Verifikasi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_verifikasi"
                            class="form-control @error('tanggal_verifikasi') is-invalid @enderror"
                            value="{{ old('tanggal_verifikasi', now()->format('Y-m-d')) }}">
                        @error('tanggal_verifikasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Verifikasi</label>
                        <input type="time" name="jam_verifikasi" class="form-control"
                            value="{{ old('jam_verifikasi', now()->format('H:i')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tenggat Tindak Lanjut</label>
                        <input type="date" name="tenggat_tindak_lanjut" class="form-control"
                            value="{{ old('tenggat_tindak_lanjut', now()->addDays(14)->format('Y-m-d')) }}">
                        <div class="form-text">Default: 14 hari sejak tanggal verifikasi</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Latitude</label>
                        <input type="number" step="any" name="koordinat_lat" class="form-control"
                            value="{{ old('koordinat_lat', $pengaduan->koordinat_lat) }}" placeholder="-6.154588">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Longitude</label>
                        <input type="number" step="any" name="koordinat_lng" class="form-control"
                            value="{{ old('koordinat_lng', $pengaduan->koordinat_lng) }}" placeholder="106.574295">
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ BAGIAN A: TIM VERIFIKATOR ══════════════════════════════ --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        A</div>
                    <div>
                        <div class="cp-title">Tim Verifikator (Identitas Pengawas LH)</div>
                        <div class="cp-sub">Daftar pegawai yang melaksanakan verifikasi lapangan</div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addTim()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Anggota
                </button>
            </div>
            <div class="cp-body">
                {{-- Header kolom --}}
                <div class="row g-2 mb-1 d-none d-md-flex">
                    <div class="col-md-3"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">Nama
                            Lengkap *</span></div>
                    <div class="col-md-3"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">NIP</span>
                    </div>
                    <div class="col-md-2"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">Pangkat/Gol</span>
                    </div>
                    <div class="col-md-3"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">Jabatan</span>
                    </div>
                </div>
                <div id="tim-container">
                    {{-- Baris pertama --}}
                    <div class="tim-row mb-2" data-index="0">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-3">
                                <input type="text" name="tim[0][nama]" class="form-control form-control-sm"
                                    placeholder="Nama lengkap" value="{{ old('tim.0.nama') }}">
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="text" name="tim[0][nip]" class="form-control form-control-sm"
                                    placeholder="NIP" value="{{ old('tim.0.nip') }}">
                            </div>
                            <div class="col-6 col-md-2">
                                <input type="text" name="tim[0][pangkat]" class="form-control form-control-sm"
                                    placeholder="III/a" value="{{ old('tim.0.pangkat') }}">
                            </div>
                            <div class="col-10 col-md-3">
                                <input type="text" name="tim[0][jabatan]" class="form-control form-control-sm"
                                    placeholder="Pengawas LH Muda" value="{{ old('tim.0.jabatan') }}">
                            </div>
                            <div class="col-2 col-md-1 text-end">
                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTim(this)"
                                    title="Hapus baris">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Quick fill dari daftar pegawai --}}
                @if ($pegawai->isNotEmpty())
                    <div class="mt-2 pt-2 border-top">
                        <div style="font-size:.78rem;color:var(--muted);margin-bottom:4px">
                            <i class="bi bi-lightning me-1"></i> Isi cepat dari data pegawai:
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($pegawai as $pg)
                                <button type="button" class="btn btn-xs btn-outline-secondary"
                                    onclick="addTimFromPegawai('{{ $pg->name }}', '{{ $pg->nip }}', '', '{{ $pg->jabatan }}')">
                                    {{ $pg->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ══ BAGIAN B: PENANGGUNG JAWAB USAHA ═══════════════════════ --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:var(--maroon-md);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        B</div>
                    <div>
                        <div class="cp-title">Identitas Penanggung Jawab Usaha / Kegiatan</div>
                        <div class="cp-sub">Pihak terlapor yang hadir saat verifikasi lapangan</div>
                    </div>
                </div>
            </div>
            <div class="cp-body">
                @php
                    $jenisTerlapor = $pengaduan?->terlapor?->jenis_terlapor ?? 'perorangan';
                    $pjLabel = [
                        'perorangan' => [
                            'nama_pj' => 'Nama Penanggung Jawab',
                            'nama_perusahaan' => null,
                            'bidang' => 'Bidang Usaha / Kegiatan',
                            'deskripsi' => 'Deskripsi Kegiatan',
                        ],
                        'lembaga' => [
                            'nama_pj' => 'Nama Penanggung Jawab',
                            'nama_perusahaan' => 'Nama Lembaga',
                            'bidang' => 'Jenis/Kategori Lembaga',
                            'deskripsi' => 'Deskripsi Kegiatan',
                        ],
                        'badan_hukum' => [
                            'nama_pj' => 'Nama Penanggung Jawab',
                            'nama_perusahaan' => 'Nama Perusahaan / Kegiatan',
                            'bidang' => 'Bidang Usaha / Kegiatan',
                            'deskripsi' => 'Deskripsi Kegiatan',
                        ],
                        'objek_lainnya' => [
                            'nama_pj' => 'Pemilik/Pengelola',
                            'nama_perusahaan' => null,
                            'bidang' => 'Jenis Objek',
                            'deskripsi' => 'Deskripsi Objek',
                        ],
                    ][$jenisTerlapor];
                @endphp
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['nama_pj'] }}</label>
                        <input type="text" name="pj_nama_pj" class="form-control" value="{{ old('pj_nama_pj') }}"
                            placeholder="Nama lengkap PJ">
                    </div>
                    @if (in_array($jenisTerlapor, ['lembaga', 'badan_hukum']))
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="pj_jabatan_pj" class="form-control"
                                value="{{ old('pj_jabatan_pj') }}" placeholder="Direktur / Manajer / dll">
                        </div>
                    @endif
                    @if ($pjLabel['nama_perusahaan'])
                        <div class="col-md-6">
                            <label class="form-label">{{ $pjLabel['nama_perusahaan'] }}</label>
                            <input type="text" name="pj_nama_perusahaan" class="form-control"
                                value="{{ old('pj_nama_perusahaan', $pengaduan?->terlapor?->nama) }}"
                                placeholder="Nama lengkap perusahaan">
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['bidang'] }}</label>
                        <input type="text" name="pj_bidang_usaha" class="form-control"
                            value="{{ old('pj_bidang_usaha', $pengaduan?->terlapor?->jenis_usaha) }}"
                            placeholder="Jenis bidang usaha">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['deskripsi'] }}</label>
                        <input type="text" name="pj_deskripsi_kegiatan" class="form-control"
                            value="{{ old('pj_deskripsi_kegiatan') }}" placeholder="Deskripsi kegiatan usaha">
                    </div>
                    @if ($jenisTerlapor === 'badan_hukum')
                        <div class="col-md-6">
                            <label class="form-label">Status Permodalan</label>
                            <input type="text" name="pj_status_permodalan" class="form-control"
                                value="{{ old('pj_status_permodalan') }}" placeholder="PMDN / PMA">
                        </div>
                        <div class="col-md-3 position-relative">
                            <label class="form-label">KBLI</label>

                            <input type="text" id="kbliSearch" class="form-control" autocomplete="off"
                                placeholder="Ketik kode atau kata kunci, mis: kertas">

                            <input type="hidden" id="kbliInput" name="pj_kbli_id" value="{{ old('pj_kbli_id') }}">

                            <div id="kbliResults" class="list-group position-absolute w-100"
                                style="z-index:1000; max-height:240px; overflow-y:auto; display:none; font-size:.82rem;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">NIB</label>
                            <input type="text" name="pj_nib" class="form-control" value="{{ old('pj_nib') }}"
                                placeholder="Nomor Induk Berusaha">
                        </div>
                    @endif
                    @if ($jenisTerlapor !== 'objek_lainnya')
                        <div class="col-md-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="pj_no_telp" class="form-control"
                                value="{{ old('pj_no_telp') }}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="pj_email" class="form-control" value="{{ old('pj_email') }}"
                                placeholder="email@perusahaan.com">
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Latitude</label>
                        <input type="number" step="any" name="pj_koordinat_lat" class="form-control"
                            value="{{ old('pj_koordinat_lat') }}" placeholder="-6.xxxxxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Longitude</label>
                        <input type="number" step="any" name="pj_koordinat_lng" class="form-control"
                            value="{{ old('pj_koordinat_lng') }}" placeholder="106.xxxxxxx">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Perusahaan / Lokasi Usaha</label>
                        <textarea name="pj_alamat" class="form-control" rows="2" placeholder="Alamat lengkap lokasi usaha / kegiatan">{{ old('pj_alamat', $pengaduan?->terlapor?->alamat) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ BAGIAN C/D/E: TEMUAN LAPANGAN ══════════════════════════ --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:#10b981;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        C</div>
                    <div>
                        <div class="cp-title">Temuan & Kesimpulan Lapangan</div>
                        <div class="cp-sub">Hasil verifikasi dan rekomendasi tindak lanjut</div>
                    </div>
                </div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">
                            <span class="badge me-1" style="background:var(--maroon)">C</span>
                            Informasi Administrasi
                        </label>
                        <textarea name="informasi_administrasi" class="rte-editor"
                            data-placeholder="Jelaskan status perizinan, dokumen lingkungan, dan informasi administrasi terkait usaha/kegiatan yang diverifikasi...">{{ old('informasi_administrasi') }}</textarea>
                        <div class="form-text">Meliputi: status izin lingkungan, AMDAL/UKL-UPL, izin operasional, dll.
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">
                            <span class="badge me-1" style="background:var(--maroon-md)">D</span>
                            Fakta Temuan Lapangan
                        </label>
                        <textarea name="fakta_temuan" class="rte-editor"
                            data-placeholder="Uraikan secara rinci fakta-fakta yang ditemukan di lapangan selama verifikasi. Meliputi kondisi fisik, hasil pengukuran, observasi, dll...">{{ old('fakta_temuan') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">
                            <span class="badge me-1" style="background:#10b981">E</span>
                            Saran dan Rekomendasi Tindak Lanjut
                        </label>
                        <textarea name="saran_tindak_lanjut" class="rte-editor"
                            data-placeholder="Tuliskan saran, rekomendasi, dan langkah tindak lanjut yang harus dilakukan oleh pihak terlapor dalam jangka waktu yang telah ditentukan...">{{ old('saran_tindak_lanjut') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ BAGIAN: SAKSI-SAKSI ════════════════════════════════════ --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div class="cp-title"><i class="bi bi-people me-1 text-muted"></i> Saksi-Saksi</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addSaksi()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Saksi
                </button>
            </div>
            <div class="cp-body">
                <div id="saksi-container">
                    <div class="saksi-row mb-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-6">
                                <input type="text" name="saksi[0][nama]" class="form-control form-control-sm"
                                    placeholder="Nama saksi" value="{{ old('saksi.0.nama') }}">
                            </div>
                            <div class="col-10 col-md-5">
                                <input type="text" name="saksi[0][jabatan]" class="form-control form-control-sm"
                                    placeholder="Jabatan (mis. Kepala Desa, Ketua RT)"
                                    value="{{ old('saksi.0.jabatan') }}">
                            </div>
                            <div class="col-2 col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSaksi(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-text">Saksi yang hadir dan menyaksikan pelaksanaan verifikasi lapangan.</div>
            </div>
        </div>

        {{-- ══ BAGIAN FOTO: DOKUMENTASI ════════════════════════════════ --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:#3b82f6;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        <i class="bi bi-camera-fill" style="font-size:.7rem"></i>
                    </div>
                    <div>
                        <div class="cp-title">Dokumentasi Foto</div>
                        <div class="cp-sub">Upload foto dokumentasi kegiatan verifikasi lapangan</div>
                    </div>
                </div>
            </div>
            <div class="cp-body">
                <div id="foto-list">
                    <div class="foto-item mb-3 p-3 rounded" style="border:1px dashed var(--border)">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label mb-1" style="font-size:.8rem">File Foto</label>
                                <input type="file" name="foto[]" class="form-control form-control-sm"
                                    accept="image/*" onchange="previewFoto(this)">
                                <div class="form-text">JPG/PNG, maks. 5MB</div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label mb-1" style="font-size:.8rem">Keterangan</label>
                                <input type="text" name="foto_keterangan[]" class="form-control form-control-sm"
                                    placeholder="Keterangan foto...">
                            </div>
                            <div class="col-md-1 text-end d-flex align-items-end">
                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeFoto(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="col-12">
                                <img src="" alt="" class="foto-preview d-none"
                                    style="max-height:120px;border-radius:4px;border:1px solid var(--border)">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addFoto()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Foto
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="draft" class="btn btn-outline-maroon">
                    <i class="bi bi-save me-1"></i> Simpan Draft
                </button>
                <button type="submit" name="action" value="save" class="btn btn-maroon px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Verifikasi
                </button>
            </div>
        </div>

    </form>
@endsection

@include('partials.rte-assets')

@push('scripts')
    <script>
        let timIdx = 1;

        let saksiIdx = document.querySelectorAll('.saksi-row').length;

        function addSaksi() {
            const i = saksiIdx++;
            const row = document.createElement('div');
            row.className = 'saksi-row mb-2';
            row.innerHTML = `
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <input type="text" name="saksi[${i}][nama]" class="form-control form-control-sm" placeholder="Nama saksi">
      </div>
      <div class="col-10 col-md-5">
        <input type="text" name="saksi[${i}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan">
      </div>
      <div class="col-2 col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSaksi(this)"><i class="bi bi-trash"></i></button>
      </div>
    </div>`;
            document.getElementById('saksi-container').appendChild(row);
        }

        function removeSaksi(btn) {
            if (document.querySelectorAll('.saksi-row').length > 1) btn.closest('.saksi-row').remove();
        }

        function addTim() {
            const i = timIdx++;
            const row = document.createElement('div');
            row.className = 'tim-row mb-2';
            row.dataset.index = i;
            row.innerHTML = `
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-3">
        <input type="text" name="tim[${i}][nama]" class="form-control form-control-sm" placeholder="Nama lengkap">
      </div>
      <div class="col-6 col-md-3">
        <input type="text" name="tim[${i}][nip]" class="form-control form-control-sm" placeholder="NIP">
      </div>
      <div class="col-6 col-md-2">
        <input type="text" name="tim[${i}][pangkat]" class="form-control form-control-sm" placeholder="III/a">
      </div>
      <div class="col-10 col-md-3">
        <input type="text" name="tim[${i}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan">
      </div>
      <div class="col-2 col-md-1 text-end">
        <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTim(this)">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </div>`;
            document.getElementById('tim-container').appendChild(row);
        }

        function addTimFromPegawai(nama, nip, pangkat, jabatan) {
            addTim();
            const rows = document.querySelectorAll('.tim-row');
            const last = rows[rows.length - 1];
            last.querySelector('[name*="[nama]"]').value = nama;
            last.querySelector('[name*="[nip]"]').value = nip;
            last.querySelector('[name*="[pangkat]"]').value = pangkat;
            last.querySelector('[name*="[jabatan]"]').value = jabatan;
        }

        function removeTim(btn) {
            const rows = document.querySelectorAll('.tim-row');
            if (rows.length > 1) {
                btn.closest('.tim-row').remove();
            }
        }

        function addFoto() {
            const tmpl = document.querySelector('.foto-item').cloneNode(true);
            tmpl.querySelector('input[type=file]').value = '';
            tmpl.querySelector('input[type=text]').value = '';
            const prev = tmpl.querySelector('.foto-preview');
            prev.src = '';
            prev.classList.add('d-none');
            document.getElementById('foto-list').appendChild(tmpl);
        }

        function removeFoto(btn) {
            const items = document.querySelectorAll('.foto-item');
            if (items.length > 1) btn.closest('.foto-item').remove();
        }

        function previewFoto(input) {
            const prev = input.closest('.foto-item').querySelector('.foto-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    prev.src = e.target.result;
                    prev.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
