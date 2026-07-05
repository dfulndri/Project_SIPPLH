@extends('layouts.admin')
@section('title', 'Daftar Pengaduan')
@section('breadcrumb', 'Pengaduan Masuk')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1 class="page-ttl">Pengaduan Masuk</h1>
    <p class="page-stl">Kelola seluruh pengaduan lingkungan hidup yang masuk ke sistem.</p>
  </div>
  <a href="{{ route('admin.pengaduan.create') }}" class="btn btn-maroon">
    <i class="bi bi-plus-circle me-1"></i> Tambah Pengaduan
  </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-check-circle-fill flex-shrink-0"></i>{{ session('success') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filter --}}
<div class="card-panel mb-3">
  <div class="cp-body py-3">
    <form method="GET" action="{{ route('admin.pengaduan.index') }}">
      <div class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Cari</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Nomor / pelapor / terlapor..." value="{{ request('search') }}">
          </div>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Pengaduan::$statusList as $key => $label)
            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Kecamatan</label>
          <select name="kecamatan_id" class="form-select form-select-sm">
            <option value="">Semua Kecamatan</option>
            @foreach($kecamatans as $k)
            <option value="{{ $k->id }}" {{ request('kecamatan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-maroon flex-fill"><i class="bi bi-funnel me-1"></i> Filter</button>
          <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-sm btn-outline-secondary flex-fill"><i class="bi bi-x-circle me-1"></i> Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tabel --}}
<div class="card-panel">
  <div class="cp-head">
    <div>
      <div class="cp-title">Data Pengaduan</div>
      <div class="cp-sub">Total: <strong>{{ $pengaduans->total() }}</strong> pengaduan ditemukan</div>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th>Nomor</th>
          <th>Tanggal</th>
          <th>Pelapor</th>
          <th>Terlapor</th>
          <th>Jenis Aduan</th>
          <th>Kecamatan</th>
          <th>Status</th>
          <th style="width:110px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengaduans as $p)
        <tr>
          <td class="text-muted" style="font-size:.75rem">{{ $pengaduans->firstItem() + $loop->index }}</td>
          <td><code style="font-size:.78rem">{{ $p->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $p->tanggal_pengaduan->format('d M Y') }}</td>
          <td style="font-size:.82rem">
            {{ $p->pelapor?->nama_display ?? '—' }}
            <br><small class="text-muted">{{ $p->pelapor?->jenis_label }}</small>
          </td>
          <td style="font-size:.82rem">{{ $p->terlapor?->nama ?? '—' }}</td>
          <td>
            @foreach($p->jenis_aduan_labels as $ja)
              <span class="badge-kat">{{ $ja }}</span>
            @endforeach
          </td>
          <td style="font-size:.82rem">{{ $p->kecamatan?->nama_kecamatan ?? '—' }}</td>
          <td><span class="badge bg-{{ $p->status_badge }}">{{ $p->status_label }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.pengaduan.show', $p) }}" class="btn btn-xs btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i></a>
              @if(in_array($p->status, ['pengaduan_baru','menunggu_disposisi']))
              <a href="{{ route('admin.pengaduan.edit', $p) }}" class="btn btn-xs btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
              <a href="{{ route('admin.disposisi.create', ['pengaduan_id' => $p->id]) }}" class="btn btn-xs btn-outline-maroon" title="Disposisi"><i class="bi bi-send"></i></a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada pengaduan masuk</div></td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $pengaduans->links() }}</div>
</div>
@endsection