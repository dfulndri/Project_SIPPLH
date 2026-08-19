@extends('layouts.pengawas')
@section('title', 'Buat Verifikasi')
@section('breadcrumb', 'Buat Verifikasi')

@section('content')

    <div class="page-hd d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-ttl">Buat Verifikasi Lapangan</h1>
            <p class="page-stl">Formulir laporan hasil verifikasi di lapangan.</p>
        </div>
        <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" style="font-size:.85rem">
            <i class="bi bi-exclamation-circle-fill me-1"></i>
            <ul class="mb-0 ps-4 mt-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Pengaduan --}}
    <div class="card-panel mb-3" style="border-left:3px solid var(--maroon)">
        <div class="cp-body py-2">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-file-text" style="color:var(--maroon);font-size:1.2rem"></i>
                <div>
                    <div style="font-size:.78rem;color:var(--muted)">Pengaduan yang diverifikasi</div>
                    <div>
                        <code>{{ $pengaduan->nomor_pengaduan }}</code>
                        <span class="ms-2" style="font-size:.85rem">— {{ $pengaduan->terlapor?->nama }}</span>
                        @foreach ($pengaduan->jenisAduanLabels as $lbl)
                            <span class="ms-1 badge-kat">{{ $lbl }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('pengawas.verifikasi.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pengaduan_id" value="{{ $pengaduan->id }}">

        {{-- Tanggal & Tenggat --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        0</div>
                    <div class="cp-title">Informasi Umum</div>
                </div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Verifikasi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_verifikasi" class="form-control"
                            value="{{ old('tanggal_verifikasi', now()->format('Y-m-d')) }}" required>
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
                        <div class="form-text">Default 14 hari</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Latitude</label>
                        <input type="text" name="koordinat_lat" class="form-control" value="{{ old('koordinat_lat') }}"
                            placeholder="-6.154588">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Koordinat Longitude</label>
                        <input type="text" name="koordinat_lng" class="form-control" value="{{ old('koordinat_lng') }}"
                            placeholder="106.574295">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tim Verifikator --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        A</div>
                    <div class="cp-title">Tim Verifikator</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addTim()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
                </button>
            </div>
            <div class="cp-body">
                <div class="row g-2 mb-1 d-none d-md-flex">
                    @foreach (['Nama *', 'NIP', 'Pangkat', 'Jabatan', ''] as $h)
                        <div class="{{ $h === '' ? 'col-md-1' : ($h === 'Nama *' ? 'col-md-3' : 'col-md-2') }}">
                            <span
                                style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">{{ $h }}</span>
                        </div>
                    @endforeach
                </div>
                <div id="tim-container">
                    <div class="tim-row mb-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-3">
                                <input type="text" name="tim[0][nama]" class="form-control form-control-sm"
                                    placeholder="Nama lengkap" value="{{ old('tim.0.nama', auth()->user()->name) }}">
                            </div>
                            <div class="col-6 col-md-2">
                                <input type="text" name="tim[0][nip]" class="form-control form-control-sm"
                                    placeholder="NIP" value="{{ old('tim.0.nip', auth()->user()->nip) }}">
                            </div>
                            <div class="col-6 col-md-2">
                                <input type="text" name="tim[0][pangkat]" class="form-control form-control-sm"
                                    placeholder="III/a">
                            </div>
                            <div class="col-10 col-md-3">
                                <input type="text" name="tim[0][jabatan]" class="form-control form-control-sm"
                                    placeholder="Jabatan" value="{{ old('tim.0.jabatan', auth()->user()->jabatan) }}">
                            </div>
                            <div class="col-2 col-md-1 text-end">
                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTim(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($pegawai->isNotEmpty())
                    <div class="mt-2 pt-2 border-top">
                        <div style="font-size:.78rem;color:var(--muted);margin-bottom:4px">Isi cepat dari data pegawai:
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($pegawai as $pg)
                                <button type="button" class="btn btn-xs btn-outline-secondary"
                                    onclick="addTimFill('{{ $pg->name }}','{{ $pg->nip }}','','{{ $pg->jabatan }}')">
                                    {{ $pg->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Penanggung Jawab --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:var(--maroon-md);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        B</div>
                    <div class="cp-title">Penanggung Jawab Usaha / Kegiatan</div>
                </div>
            </div>
            <div class="cp-body">
                @php
                    $jenisTerlapor = $pengaduan->terlapor?->jenis_terlapor ?? 'perorangan';
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
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['nama_pj'] }}</label>
                        <input type="text" name="pj_nama_pj" class="form-control" value="{{ old('pj_nama_pj') }}"
                            placeholder="Nama penanggung jawab">
                    </div>
                    @if (in_array($jenisTerlapor, ['lembaga', 'badan_hukum']))
                        <div class="col-md-6">
                            <label class="form-label">Jabatan PJ</label>
                            <input type="text" name="pj_jabatan_pj" class="form-control"
                                value="{{ old('pj_jabatan_pj') }}" placeholder="Direktur / Manajer">
                        </div>
                    @endif
                    @if ($pjLabel['nama_perusahaan'])
                        <div class="col-md-6">
                            <label class="form-label">{{ $pjLabel['nama_perusahaan'] }}</label>
                            <input type="text" name="pj_nama_perusahaan" class="form-control"
                                value="{{ old('pj_nama_perusahaan', $pengaduan->terlapor?->nama) }}">
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['bidang'] }}</label>
                        <input type="text" name="pj_bidang_usaha" class="form-control"
                            value="{{ old('pj_bidang_usaha', $pengaduan->terlapor?->jenis_usaha) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $pjLabel['deskripsi'] }}</label>
                        <input type="text" name="pj_deskripsi_kegiatan" class="form-control"
                            value="{{ old('pj_deskripsi_kegiatan') }}">
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
                        <div class="col-md-3"><label class="form-label">NIB</label>
                            <input type="text" name="pj_nib" class="form-control" value="{{ old('pj_nib') }}">
                        </div>
                    @endif
                    @if ($jenisTerlapor !== 'objek_lainnya')
                        <div class="col-md-3"><label class="form-label">Telepon</label>
                            <input type="text" name="pj_no_telp" class="form-control"
                                value="{{ old('pj_no_telp') }}">
                        </div>
                        <div class="col-md-3"><label class="form-label">Email</label>
                            <input type="email" name="pj_email" class="form-control" value="{{ old('pj_email') }}">
                        </div>
                    @endif
                    <div class="col-md-6"><label class="form-label">Koordinat Lat (Kegiatan)</label>
                        <input type="text" name="pj_koordinat_lat" class="form-control"
                            value="{{ old('pj_koordinat_lat') }}">
                    </div>
                    <div class="col-md-6"><label class="form-label">Koordinat Lng (Kegiatan)</label>
                        <input type="text" name="pj_koordinat_lng" class="form-control"
                            value="{{ old('pj_koordinat_lng') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Perusahaan</label>
                        <textarea name="pj_alamat" class="form-control" rows="2">{{ old('pj_alamat', $pengaduan->terlapor?->alamat) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Temuan C/D/E --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="d-flex align-items-center gap-2">
                    <div
                        style="width:28px;height:28px;background:#10b981;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">
                        C</div>
                    <div class="cp-title">Temuan & Kesimpulan</div>
                </div>
            </div>
            <div class="cp-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label"><span class="badge me-1" style="background:var(--maroon)">C</span>
                            Informasi Administrasi</label>
                        <textarea name="informasi_administrasi" class="rte-editor"
                            data-placeholder="Status perizinan, dokumen lingkungan, dll...">{{ old('informasi_administrasi') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label"><span class="badge me-1" style="background:var(--maroon-md)">D</span>
                            Fakta Temuan Lapangan</label>
                        <textarea name="fakta_temuan" class="rte-editor" data-placeholder="Uraikan fakta yang ditemukan di lapangan...">{{ old('fakta_temuan') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label"><span class="badge me-1" style="background:#10b981">E</span> Saran
                            Tindak Lanjut</label>
                        <textarea name="saran_tindak_lanjut" class="rte-editor"
                            data-placeholder="Rekomendasi dan langkah yang harus dilakukan...">{{ old('saran_tindak_lanjut') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saksi --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><i class="bi bi-people me-1 text-muted"></i> Saksi-Saksi</div>
                <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addSaksi()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
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
                                    placeholder="Jabatan (mis. Kepala Desa)" value="{{ old('saksi.0.jabatan') }}">
                            </div>
                            <div class="col-2 col-md-1 text-end">
                                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeSaksi(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Foto --}}
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title"><i class="bi bi-camera me-1 text-muted"></i> Dokumentasi Foto</div>
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
                                    style="max-height:80px;border-radius:4px">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addFoto()">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Foto
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-maroon px-5">
                <i class="bi bi-save me-1"></i> Simpan Verifikasi
            </button>
        </div>

    </form>
@endsection

@include('partials.rte-assets')

@push('scripts')
    <script>
        let timIdx = 1;

        function addTim() {
            const i = timIdx++;
            const r = document.createElement('div');
            r.className = 'tim-row mb-2';
            r.innerHTML = `<div class="row g-2 align-items-center">
            <div class="col-12 col-md-3"><input type="text" name="tim[${i}][nama]" class="form-control form-control-sm" placeholder="Nama"></div>
            <div class="col-6 col-md-2"><input type="text" name="tim[${i}][nip]" class="form-control form-control-sm" placeholder="NIP"></div>
            <div class="col-6 col-md-2"><input type="text" name="tim[${i}][pangkat]" class="form-control form-control-sm" placeholder="III/a"></div>
            <div class="col-10 col-md-3"><input type="text" name="tim[${i}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan"></div>
            <div class="col-2 col-md-1 text-end"><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTim(this)"><i class="bi bi-trash"></i></button></div>
        </div>`;
            document.getElementById('tim-container').appendChild(r);
        }

        function addTimFill(nama, nip, pangkat, jabatan) {
            addTim();
            const rows = document.querySelectorAll('.tim-row'),
                last = rows[rows.length - 1];
            last.querySelector('[name*="[nama]"]').value = nama;
            last.querySelector('[name*="[nip]"]').value = nip;
            last.querySelector('[name*="[pangkat]"]').value = pangkat;
            last.querySelector('[name*="[jabatan]"]').value = jabatan;
        }

        function removeTim(btn) {
            if (document.querySelectorAll('.tim-row').length > 1) btn.closest('.tim-row').remove();
        }

        let saksiIdx = document.querySelectorAll('.saksi-row').length;

        function addSaksi() {
            const i = saksiIdx++;
            const r = document.createElement('div');
            r.className = 'saksi-row mb-2';
            r.innerHTML = `<div class="row g-2 align-items-center">
            <div class="col-12 col-md-6"><input type="text" name="saksi[${i}][nama]" class="form-control form-control-sm" placeholder="Nama saksi"></div>
            <div class="col-10 col-md-5"><input type="text" name="saksi[${i}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan"></div>
            <div class="col-2 col-md-1 text-end"><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeSaksi(this)"><i class="bi bi-trash"></i></button></div>
        </div>`;
            document.getElementById('saksi-container').appendChild(r);
        }

        function removeSaksi(btn) {
            if (document.querySelectorAll('.saksi-row').length > 1) btn.closest('.saksi-row').remove();
        }

        function addFoto() {
            const t = document.querySelector('.foto-item').cloneNode(true);
            t.querySelector('input[type=file]').value = '';
            t.querySelector('input[type=text]').value = '';
            const p = t.querySelector('.foto-preview');
            p.src = '';
            p.classList.add('d-none');
            document.getElementById('foto-list').appendChild(t);
        }

        function removeFoto(btn) {
            if (document.querySelectorAll('.foto-item').length > 1) btn.closest('.foto-item').remove();
        }

        function previewFoto(inp) {
            const p = inp.closest('.foto-item').querySelector('.foto-preview');
            if (inp.files[0]) {
                const r = new FileReader();
                r.onload = e => {
                    p.src = e.target.result;
                    p.classList.remove('d-none')
                };
                r.readAsDataURL(inp.files[0]);
            }
        }

        function previewFoto(inp) {
            const p = inp.closest('.foto-item').querySelector('.foto-preview');
            if (inp.files[0]) {
                const r = new FileReader();
                r.onload = e => {
                    p.src = e.target.result;
                    p.classList.remove('d-none')
                };
                r.readAsDataURL(inp.files[0]);
            }
        }

        // ── Pencarian KBLI ──────────────────────────────────────────
        (function() {
            const search = document.getElementById('kbliSearch');
            const hidden = document.getElementById('kbliInput');
            const results = document.getElementById('kbliResults');
            if (!search) return; // jaga-jaga kalau elemen belum ada di halaman ini
            let timer = null;

            search.addEventListener('input', function() {
                clearTimeout(timer);
                hidden.value = '';
                const q = this.value.trim();
                if (q.length < 2) {
                    results.style.display = 'none';
                    return;
                }

                timer = setTimeout(() => {
                    fetch(`{{ route('pengawas.master.kbli.json') }}?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            results.innerHTML = '';
                            if (data.length === 0) {
                                results.style.display = 'none';
                                return;
                            }
                            data.forEach(item => {
                                const a = document.createElement('a');
                                a.href = '#';
                                a.className = 'list-group-item list-group-item-action';
                                a.innerHTML =
                                    `<strong>${item.kode_kbli}</strong> — ${item.judul}`;
                                a.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    search.value =
                                        `${item.kode_kbli} - ${item.judul}`;
                                    hidden.value = item.id;
                                    results.style.display = 'none';
                                });
                                results.appendChild(a);
                            });
                            results.style.display = 'block';
                        });
                }, 250);
            });

            document.addEventListener('click', function(e) {
                if (!search.contains(e.target) && !results.contains(e.target)) {
                    results.style.display = 'none';
                }
            });
        })();
    </script>
@endpush
