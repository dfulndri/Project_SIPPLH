@extends('layouts.pengawas')
@section('title', 'Tindak Lanjut')
@section('breadcrumb', 'Tindak Lanjut')

@section('content')
<div class="page-hd mb-3">
  <h1 class="page-ttl">Tindak Lanjut</h1>
  <p class="page-stl">Daftar tindak lanjut pengaduan yang ditugaskan kepada Anda</p>
</div>

<div class="card-panel">
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr><th>No. Pengaduan</th><th>Terlapor</th><th>Tanggal</th><th>Catatan</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($tindakLanjuts as $tl)
        <tr>
          <td><code>{{ $tl->pengaduan?->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $tl->pengaduan?->terlapor?->nama ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $tl->tanggal->format('d M Y') }}</td>
          <td style="font-size:.82rem;max-width:300px" class="text-truncate">{{ $tl->catatan }}</td>
          <td><span class="badge bg-{{ $tl->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($tl->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-arrow-repeat"></i>Belum ada tindak lanjut</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $tindakLanjuts->links() }}</div>
</div>
@endsection
