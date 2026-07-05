@extends('layouts.admin')
@section('title', 'Disposisi')
@section('breadcrumb', 'Disposisi')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Disposisi Pengaduan</h1>
    <p class="page-stl">Daftar pengaduan yang telah didisposisikan ke pengawas</p>
  </div>
  <a href="{{ route('admin.disposisi.create') }}" class="btn btn-sm btn-maroon"><i class="bi bi-plus-lg me-1"></i>Buat Disposisi</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-panel">
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr>
        <th>No. Pengaduan</th><th>Terlapor</th><th>Pengawas</th><th>Jadwal Verifikasi</th><th>Tgl Disposisi</th><th>Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($disposisis as $d)
        <tr>
          <td><code>{{ $d->pengaduan?->nomor_pengaduan }}</code></td>
          <td>{{ $d->pengaduan?->terlapor?->nama ?? '—' }}</td>
          <td><span class="badge bg-info text-dark">{{ $d->pengawas?->name }}</span></td>
          <td>{{ $d->jadwal_verifikasi->format('d M Y') }}</td>
          <td style="font-size:.82rem">{{ $d->created_at->format('d M Y H:i') }}</td>
          <td>
            <a href="{{ route('admin.disposisi.show', $d) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-send"></i>Belum ada disposisi</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $disposisis->links() }}</div>
</div>
@endsection
