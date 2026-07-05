@extends('layouts.admin')
@section('title','Verifikasi Lapangan')
@section('breadcrumb','Verifikasi Lapangan')

@section('content')

<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1 class="page-ttl">Verifikasi Lapangan</h1>
    <p class="page-stl">Daftar seluruh kegiatan verifikasi lapangan yang telah dilakukan.</p>
  </div>
  <a href="{{ route('admin.verifikasi.create') }}" class="btn btn-maroon">
    <i class="bi bi-plus-circle me-1"></i> Buat Verifikasi
  </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filter --}}
<div class="card-panel mb-3">
  <div class="cp-body py-3">
    <form method="GET" action="{{ route('admin.verifikasi.index') }}">
      <div class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">Semua Status</option>
            <option value="draft"   {{ request('status')=='draft'   ? 'selected' : '' }}>Draft</option>
            <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
          </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-maroon flex-fill">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
          <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tabel --}}
<div class="card-panel">
  <div class="cp-head">
    <div>
      <div class="cp-title">Data Verifikasi Lapangan</div>
      <div class="cp-sub">Total: <strong>{{ $verifikasis->total() }}</strong> verifikasi</div>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th>Tanggal Verifikasi</th>
          <th>Nomor Pengaduan</th>
          <th>Terlapor</th>
          <th>Tim Verifikator</th>
          <th>Dibuat oleh</th>
          <th>Tenggat</th>
          <th>Status</th>
          <th style="width:100px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($verifikasis as $v)
        <tr>
          <td class="text-muted" style="font-size:.75rem">{{ $verifikasis->firstItem() + $loop->index }}</td>
          <td style="white-space:nowrap;font-size:.82rem">
            {{ $v->tanggal_verifikasi->format('d M Y') }}
          </td>
          <td>
            @if($v->pengaduan)
            <a href="{{ route('admin.pengaduan.show', $v->pengaduan) }}" style="font-size:.78rem">
              <code>{{ $v->pengaduan->nomor_pengaduan }}</code>
            </a>
            @else
            <span class="text-muted">—</span>
            @endif
          </td>
          <td style="font-size:.82rem">{{ $v->pengaduan?->terlapor?->nama ?? '—' }}</td>
          <td>
            <span class="badge" style="background:rgba(106,0,0,.08);color:var(--maroon);font-size:.72rem">
              {{ $v->timVerifikator->count() ?? '?' }} orang
            </span>
          </td>
          <td style="font-size:.82rem">{{ $v->pembuat?->name ?? '—' }}</td>
          <td style="font-size:.82rem;white-space:nowrap">
            @if($v->tenggat_tindak_lanjut)
              @php $lewat = $v->tenggat_tindak_lanjut->isPast(); @endphp
              <span class="{{ $lewat ? 'text-danger' : 'text-muted' }}">
                {{ $v->tenggat_tindak_lanjut->format('d M Y') }}
                @if($lewat) <i class="bi bi-exclamation-circle-fill" title="Sudah lewat tenggat"></i> @endif
              </span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            <span class="badge bg-{{ $v->status === 'selesai' ? 'success' : 'warning' }}">
              {{ ucfirst($v->status) }}
            </span>
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.verifikasi.show', $v) }}"
                class="btn btn-xs btn-outline-secondary" title="Detail">
                <i class="bi bi-eye"></i>
              </a>
              @if($v->status === 'draft')
              <a href="{{ route('admin.verifikasi.edit', $v) }}"
                class="btn btn-xs btn-outline-warning" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="bi bi-clipboard-x"></i>
              Belum ada data verifikasi lapangan.
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($verifikasis->hasPages())
  <div class="p-3 border-top">{{ $verifikasis->links() }}</div>
  @endif
</div>

@endsection