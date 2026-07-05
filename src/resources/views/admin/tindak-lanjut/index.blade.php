@extends('layouts.admin')
@section('title', 'Tindak Lanjut')
@section('breadcrumb', 'Tindak Lanjut')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Tindak Lanjut</h1>
    <p class="page-stl">Tindakan lanjutan setelah verifikasi lapangan selesai</p>
  </div>
  <a href="{{ route('admin.tindak-lanjut.create') }}" class="btn btn-sm btn-maroon"><i class="bi bi-plus-lg me-1"></i>Tambah Tindak Lanjut</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title">Daftar Tindak Lanjut</div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.tindak-lanjut.index', ['status' => 'proses']) }}" class="btn btn-xs {{ request('status') == 'proses' ? 'btn-warning' : 'btn-outline-secondary' }}">Proses</a>
      <a href="{{ route('admin.tindak-lanjut.index', ['status' => 'selesai']) }}" class="btn btn-xs {{ request('status') == 'selesai' ? 'btn-success' : 'btn-outline-secondary' }}">Selesai</a>
      <a href="{{ route('admin.tindak-lanjut.index') }}" class="btn btn-xs {{ !request('status') ? 'btn-maroon' : 'btn-outline-secondary' }}">Semua</a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr><th>No. Pengaduan</th><th>Terlapor</th><th>Tanggal</th><th>Catatan</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($tindakLanjuts as $tl)
        <tr>
          <td><code>{{ $tl->pengaduan?->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $tl->pengaduan?->terlapor?->nama ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $tl->tanggal->format('d M Y') }}</td>
          <td style="font-size:.82rem;max-width:250px" class="text-truncate">{{ $tl->catatan }}</td>
          <td>
            <span class="badge bg-{{ $tl->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($tl->status) }}</span>
          </td>
          <td>
            @if($tl->status == 'proses')
            <form action="{{ route('admin.tindak-lanjut.selesai', $tl) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Selesaikan tindak lanjut ini?')">
              @csrf @method('PATCH')
              <input type="hidden" name="hasil" value="Selesai ditindaklanjuti">
              <button class="btn btn-xs btn-success"><i class="bi bi-check-lg"></i></button>
            </form>
            @endif
            <a href="{{ route('admin.pengaduan.show', $tl->pengaduan) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-arrow-repeat"></i>Belum ada tindak lanjut</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $tindakLanjuts->links() }}</div>
</div>
@endsection
