@extends('layouts.admin')
@section('title', 'Detail Verifikasi')
@section('breadcrumb', 'Detail Verifikasi')

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
            style="font-size:.85rem">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
            style="font-size:.85rem">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="page-hd d-flex align-items-start justify-content-between flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-{{ $verifikasi->status === 'selesai' ? 'success' : 'warning' }} px-3 py-2"
                    style="font-size:.82rem">{{ ucfirst($verifikasi->status) }}</span>
                <span style="font-size:.82rem;color:var(--muted)">Verifikasi #{{ $verifikasi->id }}</span>
            </div>
            <h1 class="page-ttl">Berita Acara Verifikasi Lapangan</h1>
            <p class="page-stl">
                Tanggal {{ $verifikasi->tanggal_verifikasi->isoFormat('D MMMM Y') }} ·
                Dibuat oleh {{ $verifikasi->pembuat?->name }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if ($verifikasi->status === 'draft')
                <a href="{{ route('admin.verifikasi.edit', $verifikasi) }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.verifikasi.finalize', $verifikasi) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success"
                        onclick="return confirm('Selesaikan verifikasi ini? Berita Acara akan dibuat otomatis.')">
                        <i class="bi bi-check2-all me-1"></i> Selesaikan & Buat BA
                    </button>
                </form>
            @endif
            @if ($verifikasi->beritaAcara)
                <a href="{{ route('admin.berita-acara.show', $verifikasi->beritaAcara) }}" class="btn btn-sm btn-maroon">
                    <i class="bi bi-file-earmark-text me-1"></i> Lihat Berita Acara
                </a>
            @endif
            <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Pengaduan Info --}}
    @if ($verifikasi->pengaduan)
        <div class="card-panel mb-3" style="border-left:3px solid var(--maroon)">
            <div class="cp-body py-2">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <i class="bi bi-file-text" style="color:var(--maroon);font-size:1.1rem"></i>
                    </div>
                    <div class="col">
                        <span style="font-size:.78rem;color:var(--muted)">Pengaduan terkait:</span>
                        <a href="{{ route('admin.pengaduan.show', $verifikasi->pengaduan) }}" class="ms-1">
                            <code>{{ $verifikasi->pengaduan->nomor_pengaduan }}</code>
                        </a>
                        <span class="ms-1" style="font-size:.82rem">
                            — {{ $verifikasi->pengaduan->terlapor?->nama }}
                            ({{ ucwords(str_replace('_', ' ', $verifikasi->pengaduan->kategori)) }})
                        </span>
                    </div>
                    @if ($verifikasi->tenggat_tindak_lanjut)
                        <div class="col-auto">
                            <span style="font-size:.78rem;color:var(--muted)">Tenggat:</span>
                            <strong class="{{ $verifikasi->tenggat_tindak_lanjut->isPast() ? 'text-danger' : '' }} ms-1"
                                style="font-size:.82rem">
                                {{ $verifikasi->tenggat_tindak_lanjut->format('d M Y') }}
                            </strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══ SECTION A: TIM VERIFIKATOR ═════════════════════════════ --}}
    <div class="card-panel mb-3">
        <div class="cp-head">
            <div class="cp-title">
                <span class="badge me-2" style="background:var(--maroon)">A</span>
                Identitas Tim Pengawas / Verifikator
            </div>
        </div>
        <div class="table-responsive">
            <table class="table sipplh-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Nama Lengkap</th>
                        <th>NIP</th>
                        <th>Pangkat / Gol</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($verifikasi->timVerifikator as $t)
                        <tr>
                            <td>{{ $t->urutan }}</td>
                            <td><strong>{{ $t->nama }}</strong></td>
                            <td><code style="font-size:.8rem">{{ $t->nip ?: '—' }}</code></td>
                            <td>{{ $t->pangkat ?: '—' }}</td>
                            <td>{{ $t->jabatan ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3" style="font-size:.82rem">
                                <i class="bi bi-people me-1"></i> Belum ada tim verifikator
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ SECTION B: PENANGGUNG JAWAB USAHA ══════════════════════ --}}
    <div class="card-panel mb-3">
        <div class="cp-head">
            <div class="cp-title">
                <span class="badge me-2" style="background:var(--maroon-md)">B</span>
                Identitas Penanggung Jawab Usaha / Kegiatan
            </div>
        </div>
        <div class="cp-body">
            @if ($verifikasi->penanggungJawab)
                @php $pj = $verifikasi->penanggungJawab; @endphp
                <div class="row g-3">
                    @foreach ([['Nama PJ', $pj->nama_pj], ['Jabatan', $pj->jabatan_pj ?: '—'], ['Nama Perusahaan', $pj->nama_perusahaan], ['Bidang Usaha', $pj->bidang_usaha ?: '—'], ['KBLI', $pj->kbli_display ?: '—'], ['NIB', $pj->nib ?: '—'], ['No. Telepon', $pj->no_telp ?: '—'], ['Email', $pj->email ?: '—']] as [$lbl, $val])
                        <div class="col-6 col-md-3">
                            <div style="font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">
                                {{ $lbl }}</div>
                            <div style="font-size:.85rem;margin-top:3px">{{ $val }}</div>
                        </div>
                    @endforeach
                    @if ($pj->alamat_perusahaan)
                        <div class="col-12">
                            <div style="font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">
                                Alamat Perusahaan</div>
                            <div style="font-size:.85rem;margin-top:3px">{{ $pj->alamat_perusahaan }}</div>
                        </div>
                    @endif
                    @if ($pj->koordinat_lat)
                        <div class="col-12">
                            <div style="font-size:.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">
                                Koordinat</div>
                            <div style="font-size:.85rem;margin-top:3px">
                                {{ $pj->koordinat_lat }}, {{ $pj->koordinat_lng }}
                                <a href="https://maps.google.com/?q={{ $pj->koordinat_lat }},{{ $pj->koordinat_lng }}"
                                    target="_blank" class="btn btn-xs btn-outline-secondary ms-2">
                                    <i class="bi bi-geo-alt me-1"></i>Maps
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-muted" style="font-size:.85rem">
                    <i class="bi bi-info-circle me-1"></i> Data penanggung jawab usaha belum diisi.
                </div>
            @endif
        </div>
    </div>

    {{-- ══ SECTION C: INFORMASI ADMINISTRASI ══════════════════════ --}}
    <div class="card-panel mb-3">
        <div class="cp-head">
            <div class="cp-title">
                <span class="badge me-2" style="background:var(--maroon)">C</span>
                Informasi Administrasi
            </div>
        </div>
        <div class="cp-body">
            @if ($verifikasi->informasi_administrasi)
                <div class="rte-content" style="font-size:.875rem;line-height:1.8">
                    {!! $verifikasi->informasi_administrasi !!}</div>
            @else
                <div class="text-muted" style="font-size:.85rem"><i class="bi bi-dash"></i> Belum diisi</div>
            @endif
        </div>
    </div>

    {{-- ══ SECTION D: FAKTA TEMUAN ════════════════════════════════ --}}
    <div class="card-panel mb-3">
        <div class="cp-head">
            <div class="cp-title">
                <span class="badge me-2" style="background:var(--maroon-md)">D</span>
                Fakta Temuan Lapangan
            </div>
        </div>
        <div class="cp-body">
            @if ($verifikasi->fakta_temuan)
                <div class="rte-content" style="font-size:.875rem;line-height:1.8">{!! $verifikasi->fakta_temuan !!}</div>
            @else
                <div class="text-muted" style="font-size:.85rem"><i class="bi bi-dash"></i> Belum diisi</div>
            @endif
        </div>
    </div>

    {{-- ══ SECTION E: SARAN TINDAK LANJUT ════════════════════════ --}}
    <div class="card-panel mb-3">
        <div class="cp-head">
            <div class="cp-title">
                <span class="badge me-2" style="background:#10b981">E</span>
                Saran dan Rekomendasi Tindak Lanjut
            </div>
            @if ($verifikasi->tenggat_tindak_lanjut)
                <div class="d-flex align-items-center gap-1" style="font-size:.78rem;color:var(--muted)">
                    <i class="bi bi-calendar-event"></i>
                    Tenggat:
                    <strong
                        class="{{ $verifikasi->tenggat_tindak_lanjut->isPast() && $verifikasi->status != 'selesai' ? 'text-danger' : '' }} ms-1">
                        {{ $verifikasi->tenggat_tindak_lanjut->format('d M Y') }}
                    </strong>
                    <span class="ms-1">({{ $verifikasi->tenggat_tindak_lanjut->diffForHumans() }})</span>
                </div>
            @endif
        </div>
        <div class="cp-body">
            @if ($verifikasi->saran_tindak_lanjut)
                <div class="rte-content" style="font-size:.875rem;line-height:1.8">{!! $verifikasi->saran_tindak_lanjut !!}
                </div>
            @else
                <div class="text-muted" style="font-size:.85rem"><i class="bi bi-dash"></i> Belum diisi</div>
            @endif
        </div>
    </div>

    {{-- ══ DOKUMENTASI FOTO ════════════════════════════════════════ --}}
    @if ($verifikasi->dokumentasiFoto->isNotEmpty())
        <div class="card-panel mb-3">
            <div class="cp-head">
                <div class="cp-title">
                    <i class="bi bi-images me-1 text-muted"></i>
                    Dokumentasi Foto ({{ $verifikasi->dokumentasiFoto->count() }} foto)
                </div>
                @if ($verifikasi->status === 'draft')
                    <a href="{{ route('admin.verifikasi.edit', $verifikasi) }}" class="btn btn-xs btn-outline-secondary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Foto
                    </a>
                @endif
            </div>
            <div class="cp-body">
                <div class="row g-2">
                    @foreach ($verifikasi->dokumentasiFoto as $foto)
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="position-relative"
                                style="border-radius:6px;overflow:hidden;border:1px solid var(--border)">
                                <img src="{{ Storage::url($foto->path_file) }}" alt="{{ $foto->keterangan }}"
                                    style="width:100%;aspect-ratio:1;object-fit:cover;cursor:pointer"
                                    onclick="openPhoto(this.src, '{{ $foto->keterangan }}')">
                                @if ($foto->keterangan)
                                    <div
                                        style="padding:4px 6px;font-size:.68rem;background:#fff;color:var(--muted);
            border-top:1px solid var(--border);line-height:1.3">
                                        {{ $foto->keterangan }}
                                    </div>
                                @endif
                                @if ($verifikasi->status === 'draft')
                                    <form method="POST"
                                        action="{{ route('admin.verifikasi.foto.delete', [$verifikasi, $foto]) }}"
                                        style="position:absolute;top:4px;right:4px">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"
                                            onclick="return confirm('Hapus foto ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Lightbox Modal --}}
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0 pb-0">
                    <small id="photoCaption" class="text-white-50"></small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-2 text-center">
                    <img id="photoSrc" src="" alt=""
                        style="max-width:100%;max-height:75vh;border-radius:4px">
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
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

        .rte-content .ql-align-center,
        .rte-content [style*="text-align:center"] {
            text-align: center;
        }

        .rte-content .ql-align-right {
            text-align: right;
        }

        .rte-content .ql-align-justify {
            text-align: justify;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function openPhoto(src, caption) {
            document.getElementById('photoSrc').src = src;
            document.getElementById('photoCaption').textContent = caption || '';
            new bootstrap.Modal(document.getElementById('photoModal')).show();
        }
    </script>
@endpush
