@extends('layouts.pengawas')
@section('title', 'Pengaduan Saya')
@section('breadcrumb', 'Pengaduan Saya')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Pengaduan Saya</h1>
    <p class="page-stl">Daftar pengaduan yang ditugaskan kepada Anda</p>
  </div>
</div>

{{-- Filter --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
  <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-maroon' : 'btn-outline-secondary' }}">Semua</a>
  @foreach(['didisposisikan' => 'Didisposisikan', 'verifikasi_lapangan' => 'Verifikasi', 'verifikasi_selesai' => 'Selesai Verif', 'tindak_lanjut' => 'Tindak Lanjut', 'selesai' => 'Selesai'] as $key => $label)
  <a href="{{ route('pengawas.tugas.index', ['status' => $key]) }}" class="btn btn-sm {{ request('status') == $key ? 'btn-maroon' : 'btn-outline-secondary' }}">{{ $label }}</a>
  @endforeach
</div>

<div class="card-panel">
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr>
        <th>Nomor</th><th>Tanggal</th><th>Pelapor</th><th>Terlapor</th><th>Kecamatan</th><th>Status</th><th>Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($pengaduans as $p)
        <tr>
          <td><code style="font-size:.78rem">{{ $p->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $p->tanggal_pengaduan->format('d M Y') }}</td>
          <td style="font-size:.82rem">{{ $p->pelapor?->nama_display ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->terlapor?->nama ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->kecamatan?->nama_kecamatan ?? '—' }}</td>
          <td><span class="badge bg-{{ $p->status_badge }}">{{ $p->status_label }}</span></td>
          <td>
            <a href="{{ route('pengawas.tugas.show', $p) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a>
            @if(in_array($p->status, ['didisposisikan','verifikasi_lapangan']) && !$p->verifikasi)
            <a href="{{ route('pengawas.verifikasi.create', ['pengaduan_id' => $p->id]) }}" class="btn btn-xs btn-outline-maroon" title="Mulai Verifikasi"><i class="bi bi-clipboard-check"></i></a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada tugas yang ditugaskan</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $pengaduans->links() }}</div>
</div>
@endsection