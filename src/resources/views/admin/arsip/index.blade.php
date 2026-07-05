@extends('layouts.admin')
@section('title', 'Arsip Pengaduan')
@section('breadcrumb', 'Arsip')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Arsip Pengaduan</h1>
    <p class="page-stl">Pengaduan yang sudah selesai dan diarsipkan</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title"><i class="bi bi-archive me-1"></i>Daftar Arsip</div>
    <form method="GET" class="d-flex gap-2">
      <input type="text" name="search" class="form-control form-control-sm" style="width:220px" placeholder="Cari nomor, pelapor, terlapor..." value="{{ request('search') }}">
      <button class="btn btn-sm btn-outline-maroon"><i class="bi bi-search"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr>
        <th>Nomor</th><th>Tanggal</th><th>Pelapor</th><th>Terlapor</th><th>Kecamatan</th><th>Status</th><th>Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($pengaduans as $p)
        <tr>
          <td><code>{{ $p->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $p->tanggal_pengaduan->format('d M Y') }}</td>
          <td style="font-size:.82rem">{{ $p->pelapor?->nama_display ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->terlapor?->nama ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->kecamatan?->nama_kecamatan ?? '—' }}</td>
          <td><span class="badge bg-{{ $p->status_badge }}">{{ $p->status_label }}</span></td>
          <td class="d-flex gap-1">
            <a href="{{ route('admin.pengaduan.show', $p) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a>
            @if($p->status !== 'arsip')
            <form action="{{ route('admin.arsip.archive', $p) }}" method="POST" onsubmit="return confirm('Arsipkan pengaduan ini?')">
              @csrf @method('PATCH')
              <button class="btn btn-xs btn-outline-maroon"><i class="bi bi-archive"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-archive"></i>Belum ada arsip</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $pengaduans->links() }}</div>
</div>
@endsection
