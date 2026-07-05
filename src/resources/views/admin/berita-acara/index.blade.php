@extends('layouts.admin')
@section('title','Berita Acara')
@section('breadcrumb','Berita Acara')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1 class="page-ttl">Berita Acara</h1>
    <p class="page-stl">Daftar seluruh Berita Acara Hasil Verifikasi Lapangan yang telah diterbitkan.</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-panel mb-3">
  <div class="cp-body py-3">
    <form method="GET">
      <div class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">Semua</option>
            <option value="draft"  {{ request('status')=='draft'  ? 'selected':'' }}>Draft</option>
            <option value="final"  {{ request('status')=='final'  ? 'selected':'' }}>Final</option>
          </select>
        </div>
        <div class="col-6 col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-maroon flex-fill">Filter</button>
          <a href="{{ route('admin.berita-acara.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title">Data Berita Acara</div>
    <div class="cp-sub">Total: {{ $beritaAcaras->total() }} BA</div>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nomor BA</th>
          <th>Tanggal Terbit</th>
          <th>Nomor Pengaduan</th>
          <th>Terlapor</th>
          <th>Dibuat oleh</th>
          <th>QR Token</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($beritaAcaras as $ba)
        <tr>
          <td class="text-muted" style="font-size:.75rem">{{ $beritaAcaras->firstItem() + $loop->index }}</td>
          <td><code style="font-size:.78rem">{{ $ba->nomor_ba }}</code></td>
          <td style="font-size:.82rem;white-space:nowrap">{{ $ba->tanggal_terbit->format('d M Y') }}</td>
          <td>
            @if($ba->verifikasi?->pengaduan)
            <a href="{{ route('admin.pengaduan.show', $ba->verifikasi->pengaduan) }}" style="font-size:.78rem">
              <code>{{ $ba->verifikasi->pengaduan->nomor_pengaduan }}</code>
            </a>
            @else —
            @endif
          </td>
          <td style="font-size:.82rem">{{ $ba->verifikasi?->pengaduan?->terlapor?->nama ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $ba->pembuat?->name ?? '—' }}</td>
          <td>
            <span title="{{ $ba->qr_code_token }}" style="font-size:.7rem;color:var(--muted);font-family:var(--font-mono)">
              {{ substr($ba->qr_code_token,0,8) }}...
            </span>
          </td>
          <td>
            <span class="badge bg-{{ $ba->status === 'final' ? 'success' : 'warning' }}">
              {{ ucfirst($ba->status) }}
            </span>
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.berita-acara.show', $ba) }}" class="btn btn-xs btn-outline-secondary" title="Detail">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('admin.berita-acara.pdf', $ba) }}" class="btn btn-xs btn-outline-danger" title="Download PDF" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i>
              </a>
              @if($ba->status === 'draft')
              <form method="POST" action="{{ route('admin.berita-acara.finalize', $ba) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-xs btn-outline-success" title="Finalisasi"
                  onclick="return confirm('Finalisasi BA ini? Status tidak bisa dikembalikan ke draft.')">
                  <i class="bi bi-check-all"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="bi bi-file-earmark-x"></i>
              Belum ada Berita Acara. Selesaikan verifikasi lapangan untuk membuat BA otomatis.
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($beritaAcaras->hasPages())
  <div class="p-3 border-top">{{ $beritaAcaras->links() }}</div>
  @endif
</div>
@endsection