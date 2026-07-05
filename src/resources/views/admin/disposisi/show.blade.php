@extends('layouts.admin')
@section('title', 'Detail Disposisi')
@section('breadcrumb', 'Disposisi / Detail')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Detail Disposisi</h1>
    <p class="page-stl">{{ $disposisi->pengaduan?->nomor_pengaduan }}</p>
  </div>
  <a href="{{ route('admin.disposisi.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-send-fill me-1"></i>Informasi Disposisi</div></div>
      <div class="cp-body">
        <table class="table table-sm" style="font-size:.84rem">
          <tr><td class="text-muted" style="width:160px">No. Pengaduan</td><td><code>{{ $disposisi->pengaduan?->nomor_pengaduan }}</code></td></tr>
          <tr><td class="text-muted">Pengawas</td><td><strong>{{ $disposisi->pengawas?->name }}</strong></td></tr>
          <tr><td class="text-muted">Jadwal Verifikasi</td><td>{{ $disposisi->jadwal_verifikasi->format('d M Y') }}</td></tr>
          <tr><td class="text-muted">Didisposisikan Oleh</td><td>{{ $disposisi->pembuat?->name }}</td></tr>
          <tr><td class="text-muted">Tanggal Disposisi</td><td>{{ $disposisi->created_at->format('d M Y H:i') }}</td></tr>
          @if($disposisi->catatan)
          <tr><td class="text-muted">Catatan</td><td>{{ $disposisi->catatan }}</td></tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-file-earmark-text me-1"></i>Detail Pengaduan</div></div>
      <div class="cp-body">
        <table class="table table-sm" style="font-size:.84rem">
          <tr><td class="text-muted" style="width:130px">Pelapor</td><td>{{ $disposisi->pengaduan?->pelapor?->nama_display ?? '—' }}</td></tr>
          <tr><td class="text-muted">Terlapor</td><td>{{ $disposisi->pengaduan?->terlapor?->nama ?? '—' }}</td></tr>
          <tr><td class="text-muted">Kecamatan</td><td>{{ $disposisi->pengaduan?->kecamatan?->nama_kecamatan ?? '—' }}</td></tr>
          <tr><td class="text-muted">Status</td><td><span class="badge bg-{{ $disposisi->pengaduan?->status_badge }}">{{ $disposisi->pengaduan?->status_label }}</span></td></tr>
        </table>
        <a href="{{ route('admin.pengaduan.show', $disposisi->pengaduan) }}" class="btn btn-sm btn-outline-maroon mt-2">
          <i class="bi bi-eye me-1"></i>Lihat Detail Pengaduan
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
