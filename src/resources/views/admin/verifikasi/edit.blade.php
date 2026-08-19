@extends('layouts.admin')
@section('title', 'Edit Verifikasi')
@section('breadcrumb', 'Edit Verifikasi')

@section('content')

    <div class="page-hd d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-ttl">Edit Verifikasi Lapangan</h1>
            <p class="page-stl">Memperbarui data verifikasi untuk pengaduan
                <code>{{ $verifikasi->pengaduan?->nomor_pengaduan }}</code>
            </p>
        </div>
        <a href="{{ route('admin.verifikasi.show', $verifikasi) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" style="font-size:.85rem">
            <strong><i class="bi bi-exclamation-circle-fill me-1"></i> Periksa isian:</strong>
            <ul class="mb-0 mt-1 ps-4">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.verifikasi.update', $verifikasi) }}" enctype="multipart/form-data">
        @csrf @method('PATCH')

        {{-- Tanggal & Tenggat --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title">Informasi Umum</div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Verifikasi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_verifikasi" class="form-control"
                            value="{{ old('tanggal_verifikasi', $verifikasi->tanggal_verifikasi->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jam Verifikasi</label>
                        <input type="time" name="jam_verifikasi" class="form-control"
                            value="{{ old('jam_verifikasi', $verifikasi->jam_verifikasi ? \Illuminate\Support\Str::substr($verifikasi->jam_verifikasi, 0, 5) : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tenggat Tindak Lanjut</label>
                        <input type="date" name="tenggat_tindak_lanjut" class="form-control"
                            value="{{ old('tenggat_tindak_lanjut', $verifikasi->tenggat_tindak_lanjut?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Latitude</label>
                        <input type="number" step="any" name="koordinat_lat" class="form-control"
                            value="{{ old('koordinat_lat', $verifikasi->koordinat_lat) }}" placeholder="-6.154588">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Longitude</label>
                        <input type="number" step="any" name="koordinat_lng" class="form-control"
                            value="{{ old('koordinat_lng', $verifikasi->koordinat_lng) }}" placeholder="106.574295">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tim Verifikator --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><span class="badge me-2" style="background:var(--maroon)">A</span>Tim Verifikator
                </div>
                <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addTim()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
                </button>
            </div>
            <div class="cp-body">
                <div class="row g-2 mb-1 d-none d-md-flex">
                    <div class="col-md-3"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">Nama
                            *</span></div>
                    <div class="col-md-3"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">NIP</span>
                    </div>
                    <div class="col-md-2"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">Pangkat</span>
                    </div>
                    <div class="col-md-3"><span
                            style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">Jabatan</span>
                    </div>
                </div>
                <div id="tim-container">
                    @forelse($verifikasi->timVerifikator as $i => $t)
                        <div class="tim-row mb-2" data-index="{{ $i }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-3">
                                    <input type="text" name="tim[{{ $i }}][nama]"
                                        class="form-control form-control-sm" value="{{ old("tim.$i.nama", $t->nama) }}"
                                        placeholder="Nama lengkap">
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="text" name="tim[{{ $i }}][nip]"
                                        class="form-control form-control-sm" value="{{ old("tim.$i.nip", $t->nip) }}"
                                        placeholder="NIP">
                                </div>
                                <div class="col-6 col-md-2">
                                    <input type="text" name="tim[{{ $i }}][pangkat]"
                                        class="form-control form-control-sm"
                                        value="{{ old("tim.$i.pangkat", $t->pangkat) }}" placeholder="III/a">
                                </div>
                                <div class="col-10 col-md-3">
                                    <input type="text" name="tim[{{ $i }}][jabatan]"
                                        class="form-control form-control-sm"
                                        value="{{ old("tim.$i.jabatan", $t->jabatan) }}" placeholder="Jabatan">
                                </div>
                                <div class="col-2 col-md-1 text-end">
                                    <button type="button" class="btn btn-xs btn-outline-danger"
                                        onclick="removeTim(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="tim-row mb-2" data-index="0">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-3">
                                    <input type="text" name="tim[0][nama]" class="form-control form-control-sm"
                                        placeholder="Nama lengkap">
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="text" name="tim[0][nip]" class="form-control form-control-sm"
                                        placeholder="NIP">
                                </div>
                                <div class="col-6 col-md-2">
                                    <input type="text" name="tim[0][pangkat]" class="form-control form-control-sm"
                                        placeholder="III/a">
                                </div>
                                <div class="col-10 col-md-3">
                                    <input type="text" name="tim[0][jabatan]" class="form-control form-control-sm"
                                        placeholder="Jabatan">
                                </div>
                                <div class="col-2 col-md-1 text-end">
                                    <button type="button" class="btn btn-xs btn-outline-danger"
                                        onclick="removeTim(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                @if ($pegawai->isNotEmpty())
                    <div class="mt-2 pt-2 border-top">
                        <div style="font-size:.78rem;color:var(--muted);margin-bottom:4px">Isi cepat dari data pegawai:
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($pegawai as $pg)
                                <button type="button" class="btn btn-xs btn-outline-secondary"
                                    onclick="addTimFromPegawai('{{ $pg->name }}','{{ $pg->nip }}','','{{ $pg->jabatan }}')">
                                    {{ $pg->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Penanggung Jawab --}}
        @php
            $pj = $verifikasi->penanggungJawab;
            $pengaduan = $verifikasi->pengaduan;
            $jenisTerlapor = $pengaduan?->terlapor?->jenis_terlapor ?? 'perorangan';
            $pjLabel = [
                'perorangan' => [
                    'nama_pj' => 'Nama PJ',
                    'nama_perusahaan' => null,
                    'bidang' => 'Bidang Usaha',
                    'deskripsi' => 'Deskripsi Kegiatan',
                ],
                'lembaga' => [
                    'nama_pj' => 'Nama PJ',
                    'nama_perusahaan' => 'Nama Lembaga',
                    'bidang' => 'Jenis/Kategori Lembaga',
                    'deskripsi' => 'Deskripsi Kegiatan',
                ],
                'badan_hukum' => [
                    'nama_pj' => 'Nama PJ',
                    'nama_perusahaan' => 'Nama Perusahaan',
                    'bidang' => 'Bidang Usaha',
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
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><span class="badge me-2" style="background:var(--maroon-md)">B</span>Penanggung
                    Jawab Usaha</div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['nama_pj'] }}</label>
                        <input type="text" name="pj_nama_pj" class="form-control"
                            value="{{ old('pj_nama_pj', $pj?->nama_pj) }}">
                    </div>
                    @if (in_array($jenisTerlapor, ['lembaga', 'badan_hukum']))
                        <div class="col-md-6">
                            <label class="form-label">Jabatan PJ</label>
                            <input type="text" name="pj_jabatan_pj" class="form-control"
                                value="{{ old('pj_jabatan_pj', $pj?->jabatan_pj) }}">
                        </div>
                    @endif
                    @if ($pjLabel['nama_perusahaan'])
                        <div class="col-md-6">
                            <label class="form-label">{{ $pjLabel['nama_perusahaan'] }}</label>
                            <input type="text" name="pj_nama_perusahaan" class="form-control"
                                value="{{ old('pj_nama_perusahaan', $pj?->nama_perusahaan) }}">
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['bidang'] }}</label>
                        <input type="text" name="pj_bidang_usaha" class="form-control"
                            value="{{ old('pj_bidang_usaha', $pj?->bidang_usaha) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['deskripsi'] }}</label>
                        <input type="text" name="pj_deskripsi_kegiatan" class="form-control"
                            value="{{ old('pj_deskripsi_kegiatan', $pj?->deskripsi_kegiatan) }}">
                    </div>
                    @if ($jenisTerlapor === 'badan_hukum')
                        <div class="col-md-6">
                            <label class="form-label">Status Permodalan</label>
                            <input type="text" name="pj_status_permodalan" class="form-control"
                                value="{{ old('pj_status_permodalan', $pj?->status_permodalan) }}"
                                placeholder="PMDN / PMA">
                        </div>
                        <div class="col-md-3 position-relative">
                            <label class="form-label">KBLI</label>
                            <input type="text" id="kbliSearch" class="form-control" autocomplete="off"
                                value="{{ old('pj_kbli_display', $pj?->kbli_display) }}"
                                placeholder="Ketik kode atau kata kunci, mis: kertas">
                            <input type="hidden" id="kbliInput" name="pj_kbli_id"
                                value="{{ old('pj_kbli_id', $pj?->kbli_id) }}">
                            <div id="kbliResults" class="list-group position-absolute w-100"
                                style="z-index:1000; max-height:240px; overflow-y:auto; display:none; font-size:.82rem;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">NIB</label>
                            <input type="text" name="pj_nib" class="form-control"
                                value="{{ old('pj_nib', $pj?->nib) }}">
                        </div>
                    @endif
                    @if ($jenisTerlapor !== 'objek_lainnya')
                        <div class="col-md-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="pj_no_telp" class="form-control"
                                value="{{ old('pj_no_telp', $pj?->no_telp) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="pj_email" class="form-control"
                                value="{{ old('pj_email', $pj?->email) }}">
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Lat</label>
                        <input type="number" step="any" name="pj_koordinat_lat" class="form-control"
                            value="{{ old('pj_koordinat_lat', $pj?->koordinat_lat) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Lng</label>
                        <input type="number" step="any" name="pj_koordinat_lng" class="form-control"
                            value="{{ old('pj_koordinat_lng', $pj?->koordinat_lng) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Perusahaan</label>
                        <textarea name="pj_alamat" class="form-control" rows="2">{{ old('pj_alamat', $pj?->alamat_perusahaan) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Temuan --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><span class="badge me-2" style="background:#10b981">C–E</span>Temuan Lapangan</div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label"><span class="badge me-1" style="background:var(--maroon)">C</span>
                            Informasi Administrasi</label>
                        <textarea name="informasi_administrasi" class="rte-editor"
                            data-placeholder="Status perizinan, dokumen lingkungan, dll...">{{ old('informasi_administrasi', $verifikasi->informasi_administrasi) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label"><span class="badge me-1" style="background:var(--maroon-md)">D</span>
                            Fakta Temuan</label>
                        <textarea name="fakta_temuan" class="rte-editor" data-placeholder="Uraikan fakta yang ditemukan di lapangan...">{{ old('fakta_temuan', $verifikasi->fakta_temuan) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label"><span class="badge me-1" style="background:#10b981">E</span> Saran
                            Tindak Lanjut</label>
                        <textarea name="saran_tindak_lanjut" class="rte-editor"
                            data-placeholder="Rekomendasi dan langkah yang harus dilakukan...">{{ old('saran_tindak_lanjut', $verifikasi->saran_tindak_lanjut) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saksi-Saksi --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><i class="bi bi-people me-1 text-muted"></i> Saksi-Saksi</div>
                <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addSaksi()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Saksi
                </button>
            </div>
            <div class="cp-body">
                <div id="saksi-container">
                    @php
                        $saksiRows = old(
                            'saksi',
                            $verifikasi->saksi
                                ->map(fn($s) => ['nama' => $s->nama, 'jabatan' => $s->jabatan])
                                ->toArray(),
                        );
                        if (empty($saksiRows)) {
                            $saksiRows = [['nama' => '', 'jabatan' => '']];
                        }
                    @endphp
                    @foreach ($saksiRows as $idx => $s)
                        <div class="saksi-row mb-2">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <input type="text" name="saksi[{{ $idx }}][nama]"
                                        class="form-control form-control-sm" placeholder="Nama saksi"
                                        value="{{ $s['nama'] ?? '' }}">
                                </div>
                                <div class="col-10 col-md-5">
                                    <input type="text" name="saksi[{{ $idx }}][jabatan]"
                                        class="form-control form-control-sm"
                                        placeholder="Jabatan (mis. Kepala Desa, Ketua RT)"
                                        value="{{ $s['jabatan'] ?? '' }}">
                                </div>
                                <div class="col-2 col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="removeSaksi(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Foto yang ada --}}
        @if ($verifikasi->dokumentasiFoto->isNotEmpty())
            <div class="card-panel mb-3">
                <div class="cp-head">
                    <div class="cp-title"><i class="bi bi-images me-1 text-muted"></i> Foto yang Sudah Diunggah</div>
                </div>
                <div class="cp-body">
                    <div class="row g-2">
                        @foreach ($verifikasi->dokumentasiFoto as $foto)
                            <div class="col-6 col-md-3 col-lg-2">
                                <div
                                    style="position:relative;border-radius:6px;overflow:hidden;border:1px solid var(--border)">
                                    <img src="{{ Storage::url($foto->path_file) }}" alt=""
                                        style="width:100%;aspect-ratio:1;object-fit:cover">
                                    @if ($foto->keterangan)
                                        <div style="padding:3px 5px;font-size:.65rem;background:#fff;color:var(--muted)">
                                            {{ $foto->keterangan }}</div>
                                    @endif
                                    <form method="POST"
                                        action="{{ route('admin.verifikasi.foto.delete', [$verifikasi, $foto]) }}"
                                        style="position:absolute;top:4px;right:4px">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"
                                            onclick="return confirm('Hapus foto ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Tambah Foto Baru --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><i class="bi bi-camera me-1 text-muted"></i> Tambah Foto Baru</div>
            </div>
            <div class="cp-body">
                <div id="foto-list">
                    <div class="foto-item mb-2 p-2 rounded" style="border:1px dashed var(--border)">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <input type="file" name="foto[]" class="form-control form-control-sm"
                                    accept="image/*" onchange="previewFoto(this)">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="foto_keterangan[]" class="form-control form-control-sm"
                                    placeholder="Keterangan foto">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeFoto(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="col-12">
                                <img src="" class="foto-preview d-none"
                                    style="max-height:80px;border-radius:4px;border:1px solid var(--border)">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addFoto()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Foto Lagi
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.verifikasi.show', $verifikasi) }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-maroon px-5">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>

    </form>
@endsection

@include('partials.rte-assets')

@push('scripts')
    <script>
        let timIdx = {{ $verifikasi->timVerifikator->count() ?: 1 }};

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
            if (rows.length > 1) btn.closest('.tim-row').remove();
        }

        function addFoto() {
            const tmpl = document.querySelector('.foto-item').cloneNode(true);
            tmpl.querySelector('input[type=file]').value = '';
            tmpl.querySelector('input[type=text]').value = '';
            const p = tmpl.querySelector('.foto-preview');
            p.src = '';
            p.classList.add('d-none');
            document.getElementById('foto-list').appendChild(tmpl);
        }

        function removeFoto(btn) {
            const items = document.querySelectorAll('.foto-item');
            if (items.length > 1) btn.closest('.foto-item').remove();
        }

        function previewFoto(input) {
            const prev = input.closest('.foto-item').querySelector('.foto-preview');
            if (input.files && input.files[0]) {
                const r = new FileReader();
                r.onload = e => {
                    prev.src = e.target.result;
                    prev.classList.remove('d-none');
                };
                r.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
