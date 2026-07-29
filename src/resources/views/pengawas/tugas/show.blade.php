@extends('layouts.pengawas')
@section('title', 'Detail Tugas')
@section('breadcrumb', 'Tugas / Detail')

@push('styles')
    <style>
        /* Halaman ini: card menyesuaikan tinggi konten, tidak melar (hindari ruang kosong) */
        .card-panel {
            height: auto !important;
        }

        .tf-step {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            font-size: .68rem;
            font-weight: 500;
            background: #f3f4f6;
            color: var(--muted);
        }

        .tf-step:first-child {
            border-radius: 6px 0 0 6px;
        }

        .tf-step:last-child {
            border-radius: 0 6px 6px 0;
        }

        .tf-step.done {
            background: var(--maroon);
            color: #fff;
        }

        .tf-step.current {
            background: #10b981;
            color: #fff;
        }

        .info-label {
            font-size: .78rem;
            color: var(--muted);
            font-weight: 500;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: .88rem;
            color: var(--text);
            margin-bottom: .75rem;
        }

        /* Verifikasi sections */
        .vsec-title {
            font-size: .8rem;
            font-weight: 700;
            color: var(--maroon);
            text-transform: uppercase;
            letter-spacing: .03em;
            padding-bottom: 4px;
            margin: .25rem 0 .6rem;
            border-bottom: 1px solid var(--border);
        }

        .vsec-empty {
            font-size: .82rem;
            font-style: italic;
        }

        .vtable {
            font-size: .82rem;
        }

        .vtable thead th {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: var(--muted);
            background: rgba(106, 0, 0, .05);
            border-bottom: 1px solid var(--border);
        }

        .vtable td {
            vertical-align: middle;
        }

        .vbox {
            font-size: .86rem;
            line-height: 1.7;
            background: #fafafa;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: .6rem .8rem;
            margin-top: 2px;
        }

        .rte-content p {
            margin: 0 0 .5rem;
        }

        .rte-content ul,
        .rte-content ol {
            margin: .25rem 0 .5rem;
            padding-left: 1.4rem;
        }

        .rte-content img {
            max-width: 100%;
            height: auto;
        }

        .rte-content table {
            border-collapse: collapse;
            width: 100%;
            margin: .5rem 0;
        }

        .rte-content table td,
        .rte-content table th {
            border: 1px solid #bbb;
            padding: 4px 8px;
        }

        .vsaksi {
            font-size: .86rem;
            padding: .35rem .5rem;
            background: #fafafa;
            border: 1px solid var(--border);
            border-radius: 6px;
            margin-bottom: .4rem;
        }

        .vsaksi-no {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--maroon);
            color: #fff;
            font-size: .72rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .vfoto {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: transform .15s;
        }

        .vfoto:hover {
            transform: scale(1.03);
        }
    </style>
@endpush

@section('content')
    <div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h1 class="page-ttl">Detail Tugas</h1>
            <p class="page-stl"><code>{{ $pengaduan->nomor_pengaduan }}</code></p>
        </div>
        <div class="d-flex gap-2">
            @if (in_array($pengaduan->status, ['didisposisikan', 'verifikasi_lapangan']) && !$pengaduan->verifikasi)
                <a href="{{ route('pengawas.verifikasi.create', ['pengaduan_id' => $pengaduan->id]) }}"
                    class="btn btn-sm btn-maroon"><i class="bi bi-clipboard-check me-1"></i>Mulai Verifikasi</a>
            @endif
            <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-sm btn-outline-secondary"><i
                    class="bi bi-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Workflow --}}
    @php
        $workflow = [
            'pengaduan_baru',
            'menunggu_disposisi',
            'didisposisikan',
            'verifikasi_lapangan',
            'verifikasi_selesai',
            'tindak_lanjut',
            'selesai',
            'arsip',
        ];
        $currentIdx = array_search($pengaduan->status, $workflow);
    @endphp
    <div class="d-flex flex-wrap mb-3">
        @foreach ($workflow as $i => $step)
            <div class="tf-step {{ $i < $currentIdx ? 'done' : ($i == $currentIdx ? 'current' : '') }}">
                {{ \App\Models\Pengaduan::$statusList[$step] }}
            </div>
        @endforeach
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-md-8">
            {{-- Info Pengaduan --}}
            <div class="card-panel mb-3">
                <div class="cp-head">
                    <div class="cp-title"><i class="bi bi-file-earmark-text me-1"></i>Informasi Pengaduan</div>
                </div>
                <div class="cp-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-label">Tanggal</div>
                            <div class="info-value">{{ $pengaduan->tanggal_pengaduan->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Status</div>
                            <div class="info-value"><span
                                    class="badge bg-{{ $pengaduan->status_badge }}">{{ $pengaduan->status_label }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Kecamatan</div>
                            <div class="info-value">{{ $pengaduan->kecamatan?->nama_kecamatan ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Jenis Aduan</div>
                            <div class="info-value">
                                @foreach ($pengaduan->jenis_aduan_labels as $ja)
                                    <span class="badge-kat me-1">{{ $ja }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Uraian</div>
                            <div class="info-value" style="white-space:pre-line">{{ $pengaduan->uraian_pengaduan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Disposisi --}}
            @if ($pengaduan->disposisi)
                <div class="card-panel mb-3">
                    <div class="cp-head">
                        <div class="cp-title"><i class="bi bi-send-fill me-1"></i>Disposisi</div>
                    </div>
                    <div class="cp-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-label">Jadwal Verifikasi</div>
                                <div class="info-value">{{ $pengaduan->disposisi->jadwal_verifikasi->format('d M Y') }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Didisposisikan Oleh</div>
                                <div class="info-value">{{ $pengaduan->disposisi->pembuat?->name }}</div>
                            </div>
                            @if ($pengaduan->disposisi->catatan)
                                <div class="col-md-4">
                                    <div class="info-label">Catatan</div>
                                    <div class="info-value">{{ $pengaduan->disposisi->catatan }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Verifikasi --}}
            @if ($pengaduan->verifikasi)
                @php
                    $v = $pengaduan->verifikasi;
                    $pj = $v->penanggungJawab;
                @endphp
                <div class="card-panel mb-3">
                    <div class="cp-head">
                        <div class="cp-title"><i class="bi bi-clipboard-check me-1"></i>Verifikasi Lapangan</div>
                        <div class="d-flex gap-1">
                            @if ($v->status == 'draft')
                                <a href="{{ route('pengawas.verifikasi.edit', $v) }}"
                                    class="btn btn-xs btn-outline-maroon"><i class="bi bi-pencil"></i> Edit</a>
                                <form method="POST" action="{{ route('pengawas.verifikasi.finalize', $v) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Selesaikan verifikasi ini? Berita Acara akan dibuat otomatis.')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-xs btn-success"><i class="bi bi-check2-all"></i>
                                        Selesaikan &amp; Buat BA</button>
                                </form>
                            @elseif($v->beritaAcara)
                                <a href="{{ route('pengawas.berita-acara.show', $v->beritaAcara) }}"
                                    class="btn btn-xs btn-outline-maroon"><i class="bi bi-file-text"></i> Lihat BA</a>
                                <a href="{{ route('pengawas.berita-acara.pdf', $v->beritaAcara) }}"
                                    class="btn btn-xs btn-success"><i class="bi bi-download"></i> PDF</a>
                            @endif
                        </div>
                    </div>
                    <div class="cp-body">

                        {{-- Informasi Umum --}}
                        <div class="vsec-title">Informasi Umum</div>
                        <div class="row gx-3 gy-1 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="info-label">Tanggal</div>
                                <div class="info-value">{{ $v->tanggal_verifikasi->format('d M Y') }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="info-label">Jam</div>
                                <div class="info-value">
                                    {{ $v->jam_verifikasi ? \Illuminate\Support\Str::substr($v->jam_verifikasi, 0, 5) : '—' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="info-label">Status</div>
                                <div class="info-value"><span
                                        class="badge bg-{{ $v->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($v->status) }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="info-label">Tenggat TL</div>
                                <div class="info-value">{{ $v->tenggat_tindak_lanjut?->format('d M Y') ?? '—' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Koordinat</div>
                                <div class="info-value">
                                    {{ $v->koordinat_lat && $v->koordinat_lng ? $v->koordinat_lat . ', ' . $v->koordinat_lng : '—' }}
                                </div>
                            </div>
                        </div>

                        {{-- Tim Verifikator --}}
                        <div class="vsec-title">Tim Verifikator</div>
                        @if ($v->timVerifikator->isNotEmpty())
                            <div class="table-responsive mb-3">
                                <table class="table table-sm vtable mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:32px">#</th>
                                            <th>Nama</th>
                                            <th>NIP</th>
                                            <th>Pangkat</th>
                                            <th>Jabatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($v->timVerifikator as $t)
                                            <tr>
                                                <td>{{ $t->urutan }}</td>
                                                <td class="fw-semibold">{{ $t->nama }}</td>
                                                <td>{{ $t->nip ?: '—' }}</td>
                                                <td>{{ $t->pangkat ?: '—' }}</td>
                                                <td>{{ $t->jabatan ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else<div class="text-muted vsec-empty mb-3">Belum ada tim verifikator</div>
                        @endif

                        {{-- Penanggung Jawab Usaha --}}
                        <div class="vsec-title">Penanggung Jawab Usaha / Kegiatan</div>
                        @if ($pj)
                            <div class="row gx-3 gy-1 mb-3">
                                <div class="col-6 col-md-3">
                                    <div class="info-label">Nama PJ</div>
                                    <div class="info-value">{{ $pj->nama_pj ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">Jabatan</div>
                                    <div class="info-value">{{ $pj->jabatan_pj ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">Perusahaan</div>
                                    <div class="info-value">{{ $pj->nama_perusahaan ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">Bidang Usaha</div>
                                    <div class="info-value">{{ $pj->bidang_usaha ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">KBLI</div>
                                    <div class="info-value">{{ $pj->kbli ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">NIB</div>
                                    <div class="info-value">{{ $pj->nib ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">Status Permodalan</div>
                                    <div class="info-value">{{ $pj->status_permodalan ?: '—' }}</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="info-label">Telepon</div>
                                    <div class="info-value">{{ $pj->no_telp ?: '—' }}</div>
                                </div>
                                @if ($pj->deskripsi_kegiatan)
                                    <div class="col-12">
                                        <div class="info-label">Deskripsi Kegiatan</div>
                                        <div class="info-value">{{ $pj->deskripsi_kegiatan }}</div>
                                    </div>
                                @endif
                                @if ($pj->alamat_perusahaan)
                                    <div class="col-12">
                                        <div class="info-label">Alamat</div>
                                        <div class="info-value">{{ $pj->alamat_perusahaan }}</div>
                                    </div>
                                @endif
                            </div>
                        @else<div class="text-muted vsec-empty mb-3">Belum diisi</div>
                        @endif

                        {{-- Temuan & Kesimpulan --}}
                        <div class="vsec-title">Temuan &amp; Kesimpulan</div>
                        <div class="mb-3">
                            <div class="info-label">C. Informasi Administrasi</div>
                            <div class="rte-content vbox">{!! $v->informasi_administrasi ?: '<span class="text-muted">—</span>' !!}</div>
                            <div class="info-label mt-2">D. Fakta Temuan Lapangan</div>
                            <div class="rte-content vbox">{!! $v->fakta_temuan ?: '<span class="text-muted">—</span>' !!}</div>
                            <div class="info-label mt-2">E. Saran Tindak Lanjut</div>
                            <div class="rte-content vbox">{!! $v->saran_tindak_lanjut ?: '<span class="text-muted">—</span>' !!}</div>
                        </div>

                        {{-- Saksi --}}
                        <div class="vsec-title">Saksi-Saksi</div>
                        @if ($v->saksi->isNotEmpty())
                            <div class="row gx-3 gy-1 mb-3">
                                @foreach ($v->saksi as $s)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 vsaksi">
                                            <span class="vsaksi-no">{{ $s->urutan }}</span>
                                            <div><span class="fw-semibold">{{ $s->nama }}</span>
                                                @if ($s->jabatan)
                                                    <span class="text-muted"> — {{ $s->jabatan }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else<div class="text-muted vsec-empty mb-3">Belum ada saksi</div>
                        @endif

                        {{-- Foto Tersimpan --}}
                        <div class="vsec-title">Foto Tersimpan</div>
                        @if ($v->dokumentasiFoto->isNotEmpty())
                            <div class="row g-2">
                                @foreach ($v->dokumentasiFoto as $foto)
                                    <div class="col-4 col-md-2">
                                        <img src="{{ asset('storage/' . $foto->path_file) }}" class="vfoto"
                                            onclick="vOpenFoto(this.src,'{{ $foto->keterangan }}')"
                                            alt="{{ $foto->keterangan }}">
                                    </div>
                                @endforeach
                            </div>
                        @else<div class="text-muted vsec-empty">Belum ada foto</div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- Tindak Lanjut --}}
            @if ($pengaduan->tindakLanjut->isNotEmpty())
                <div class="card-panel">
                    <div class="cp-head">
                        <div class="cp-title"><i class="bi bi-arrow-repeat me-1"></i>Tindak Lanjut</div>
                    </div>
                    <div class="cp-body">
                        @foreach ($pengaduan->tindakLanjut as $tl)
                            <div class="border rounded p-3 mb-2" style="font-size:.84rem">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong>{{ $tl->tanggal->format('d M Y') }}</strong>
                                    <span
                                        class="badge bg-{{ $tl->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($tl->status) }}</span>
                                </div>
                                <p class="mb-0">{{ $tl->catatan }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card-panel mb-3">
                <div class="cp-head">
                    <div class="cp-title"><i class="bi bi-person-badge me-1"></i>Pelapor</div>
                </div>
                <div class="cp-body">
                    <div class="info-label">Nama</div>
                    <div class="info-value">{{ $pengaduan->pelapor?->nama_display }}</div>
                    <div class="info-label">Jenis</div>
                    <div class="info-value"><span
                            class="badge bg-info text-dark">{{ $pengaduan->pelapor?->jenis_label }}</span></div>
                    @if ($pengaduan->pelapor?->no_telp)
                        <div class="info-label">Telp</div>
                        <div class="info-value">{{ $pengaduan->pelapor->no_telp }}</div>
                    @endif
                </div>
            </div>
            <div class="card-panel mb-3">
                <div class="cp-head">
                    <div class="cp-title"><i class="bi bi-exclamation-triangle me-1"></i>Terlapor</div>
                </div>
                <div class="cp-body">
                    <div class="info-label">Nama</div>
                    <div class="info-value">{{ $pengaduan->terlapor?->nama ?? '—' }}</div>
                    <div class="info-label">Jenis</div>
                    <div class="info-value"><span
                            class="badge bg-warning text-dark">{{ $pengaduan->terlapor?->jenis_label }}</span></div>
                    @if ($pengaduan->terlapor?->alamat)
                        <div class="info-label">Alamat</div>
                        <div class="info-value">{{ $pengaduan->terlapor->alamat }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <div class="modal fade" id="vPhotoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0 pb-0">
                    <small id="vPhotoCap" class="text-white-50"></small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-2 text-center">
                    <img id="vPhotoSrc" src="" style="max-width:100%;max-height:75vh;border-radius:4px">
                </div>
            </div>
        </div>
    </div>
    <script>
        function vOpenFoto(src, cap) {
            document.getElementById('vPhotoSrc').src = src;
            document.getElementById('vPhotoCap').textContent = cap || '';
            new bootstrap.Modal(document.getElementById('vPhotoModal')).show();
        }
    </script>
@endpush
