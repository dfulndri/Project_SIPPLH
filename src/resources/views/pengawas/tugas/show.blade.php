@extends('layouts.pengawas')
@section('title', 'Detail Tugas')
@section('breadcrumb', 'Tugas / Detail')

@push('styles')
    <style>
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

    <div class="row g-3">
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
                <div class="card-panel mb-3">
                    <div class="cp-head">
                        <div class="cp-title"><i class="bi bi-clipboard-check me-1"></i>Verifikasi Lapangan</div>
                        <div class="d-flex gap-1">
                            @if ($pengaduan->verifikasi->status == 'draft')
                                <a href="{{ route('pengawas.verifikasi.edit', $pengaduan->verifikasi) }}"
                                    class="btn btn-xs btn-outline-maroon"><i class="bi bi-pencil"></i> Edit</a>
                                <form method="POST"
                                    action="{{ route('pengawas.verifikasi.finalize', $pengaduan->verifikasi) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Selesaikan verifikasi ini? Berita Acara akan dibuat otomatis.')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-xs btn-success"><i class="bi bi-check2-all"></i>
                                        Selesaikan &amp; Buat BA</button>
                                </form>
                            @elseif($pengaduan->verifikasi->beritaAcara)
                                <a href="{{ route('pengawas.berita-acara.show', $pengaduan->verifikasi->beritaAcara) }}"
                                    class="btn btn-xs btn-outline-maroon"><i class="bi bi-file-text"></i> Lihat BA</a>
                                <a href="{{ route('pengawas.berita-acara.pdf', $pengaduan->verifikasi->beritaAcara) }}"
                                    class="btn btn-xs btn-success"><i class="bi bi-download"></i> PDF</a>
                            @endif
                        </div>
                    </div>
                    <div class="cp-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-label">Tanggal</div>
                                <div class="info-value">{{ $pengaduan->verifikasi->tanggal_verifikasi->format('d M Y') }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Status</div>
                                <div class="info-value"><span
                                        class="badge bg-{{ $pengaduan->verifikasi->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($pengaduan->verifikasi->status) }}</span>
                                </div>
                            </div>
                            @if ($pengaduan->verifikasi->fakta_temuan)
                                <div class="col-12">
                                    <div class="info-label">Fakta Temuan</div>
                                    <div class="info-value" style="white-space:pre-line">
                                        {{ $pengaduan->verifikasi->fakta_temuan }}</div>
                                </div>
                            @endif
                            @if ($pengaduan->verifikasi->saran_tindak_lanjut)
                                <div class="col-12">
                                    <div class="info-label">Saran Tindak Lanjut</div>
                                    <div class="info-value" style="white-space:pre-line">
                                        {{ $pengaduan->verifikasi->saran_tindak_lanjut }}</div>
                                </div>
                            @endif
                        </div>
                        @if ($pengaduan->verifikasi->dokumentasiFoto?->isNotEmpty())
                            <div class="row g-2 mt-2">
                                @foreach ($pengaduan->verifikasi->dokumentasiFoto as $foto)
                                    <div class="col-md-3"><img src="{{ asset('storage/' . $foto->path_file) }}"
                                            class="img-fluid rounded"
                                            style="max-height:120px;width:100%;object-fit:cover"></div>
                                @endforeach
                            </div>
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
