@extends('layouts.pengawas')
@section('title', 'Detail Berita Acara')
@section('breadcrumb', 'Berita Acara / Detail')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Berita Acara {{ $ba->nomor_ba }}</h1>
    <p class="page-stl">{{ $ba->verifikasi?->pengaduan?->nomor_pengaduan }}</p>
  </div>
  <a href="{{ route('pengawas.berita-acara.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-3">
  <div class="col-md-8">
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title">Informasi BA</div></div>
      <div class="cp-body">
        <table class="table table-sm" style="font-size:.84rem">
          <tr><td class="text-muted" style="width:180px">Nomor BA</td><td><code>{{ $ba->nomor_ba }}</code></td></tr>
          <tr><td class="text-muted">Tanggal Terbit</td><td>{{ $ba->tanggal_terbit->format('d M Y') }}</td></tr>
          <tr><td class="text-muted">Status</td><td><span class="badge bg-{{ $ba->status == 'final' ? 'success' : 'warning' }}">{{ ucfirst($ba->status) }}</span></td></tr>
          <tr><td class="text-muted">Terlapor</td><td>{{ $ba->verifikasi?->pengaduan?->terlapor?->nama ?? '—' }}</td></tr>
          <tr><td class="text-muted">Tanggal Verifikasi</td><td>{{ $ba->verifikasi?->tanggal_verifikasi->format('d M Y') }}</td></tr>
        </table>
      </div>
    </div>

    @if($ba->verifikasi?->timVerifikator->isNotEmpty())
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title">Tim Verifikator</div></div>
      <div class="cp-body">
        <table class="table table-sm" style="font-size:.84rem">
          <thead><tr><th>#</th><th>Nama</th><th>NIP</th><th>Jabatan</th></tr></thead>
          <tbody>
            @foreach($ba->verifikasi->timVerifikator as $tv)
            <tr>
              <td>{{ $tv->urutan }}</td><td>{{ $tv->nama }}</td><td>{{ $tv->nip ?? '—' }}</td><td>{{ $tv->jabatan ?? '—' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

    @if($ba->verifikasi?->dokumentasiFoto->isNotEmpty())
    <div class="card-panel">
      <div class="cp-head"><div class="cp-title">Dokumentasi Foto</div></div>
      <div class="cp-body">
        <div class="row g-2">
          @foreach($ba->verifikasi->dokumentasiFoto as $foto)
          <div class="col-md-4">
            <img src="{{ asset('storage/' . $foto->path_file) }}" alt="{{ $foto->keterangan }}" class="img-fluid rounded" style="max-height:180px;width:100%;object-fit:cover">
            @if($foto->keterangan)<small class="text-muted d-block mt-1">{{ $foto->keterangan }}</small>@endif
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif
  </div>

  <div class="col-md-4">
    @if($ba->verifikasi?->fakta_temuan)
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title">Fakta Temuan</div></div>
      <div class="cp-body" style="font-size:.84rem;white-space:pre-line">{{ $ba->verifikasi->fakta_temuan }}</div>
    </div>
    @endif
    @if($ba->verifikasi?->saran_tindak_lanjut)
    <div class="card-panel">
      <div class="cp-head"><div class="cp-title">Saran Tindak Lanjut</div></div>
      <div class="cp-body" style="font-size:.84rem;white-space:pre-line">{{ $ba->verifikasi->saran_tindak_lanjut }}</div>
    </div>
    @endif
  </div>
</div>
@endsection
