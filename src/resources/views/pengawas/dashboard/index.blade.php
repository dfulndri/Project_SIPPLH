@extends('layouts.pengawas')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>#mapPengawas { height:300px; border-radius:8px; z-index:0; }</style>
@endpush

@section('content')
<div class="page-hd mb-3">
  <h1 class="page-ttl">Dashboard Pengawas</h1>
  <p class="page-stl">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Berikut ringkasan tugas Anda.</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Total Tugas', $stats['total'], 'bi-folder2-open', 'var(--maroon)'],
    ['Menunggu Verifikasi', $stats['pending'], 'bi-hourglass-split', '#f59e0b'],
    ['Verifikasi Selesai', $stats['verifikasi'], 'bi-clipboard-check-fill', '#3b82f6'],
    ['Pengaduan Selesai', $stats['selesai'], 'bi-check-circle-fill', '#10b981'],
  ] as [$label, $val, $icon, $color])
  <div class="col-6 col-md-3">
    <div class="stat-card" style="border-top-color:{{ $color }}">
      <div><div class="sc-num">{{ $val }}</div><div class="sc-lbl">{{ $label }}</div></div>
      <i class="bi {{ $icon }} sc-ic" style="color:{{ $color }}"></i>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-3 mb-4">
  {{-- Tugas Terbaru --}}
  <div class="col-md-7">
    <div class="card-panel h-100">
      <div class="cp-head">
        <div class="cp-title">Tugas Terbaru</div>
        <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-xs btn-outline-maroon">Semua <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="table-responsive">
        <table class="table sipplh-table">
          <thead><tr><th>Nomor</th><th>Terlapor</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @forelse($tugasTerbaru as $t)
            <tr>
              <td><code style="font-size:.75rem">{{ $t->nomor_pengaduan }}</code></td>
              <td style="font-size:.82rem">{{ $t->terlapor?->nama ?? '—' }}</td>
              <td><span class="badge bg-{{ $t->status_badge }}">{{ $t->status_label }}</span></td>
              <td><a href="{{ route('pengawas.tugas.show', $t) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
            </tr>
            @empty
            <tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada tugas</div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Map --}}
  <div class="col-md-5">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-geo-alt-fill me-1" style="color:var(--maroon)"></i>Lokasi Tugas</div></div>
      <div class="cp-body p-0"><div id="mapPengawas"></div></div>
    </div>
  </div>
</div>

{{-- Verifikasi Terakhir --}}
@if($verifikasiSaya->isNotEmpty())
<div class="card-panel">
  <div class="cp-head"><div class="cp-title">Verifikasi Terakhir Saya</div></div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr><th>No. Pengaduan</th><th>Terlapor</th><th>Tanggal</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($verifikasiSaya as $v)
        <tr>
          <td><code style="font-size:.75rem">{{ $v->pengaduan?->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $v->pengaduan?->terlapor?->nama ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $v->tanggal_verifikasi->format('d M Y') }}</td>
          <td><span class="badge bg-{{ $v->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($v->status) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('mapPengawas').setView([-6.2, 106.63], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OSM', maxZoom:18}).addTo(map);
const mapData = @json($mapData);
mapData.forEach(p => {
  const icon = L.divIcon({className:'',html:`<div style="width:12px;height:12px;border-radius:50%;background:var(--maroon);border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.4)"></div>`,iconSize:[12,12],iconAnchor:[6,6]});
  L.marker([p.lat, p.lng], {icon}).addTo(map).bindPopup(`<strong>${p.nomor}</strong><br>${p.terlapor}<br><span class="badge" style="background:#6A0000;color:#fff;font-size:10px">${p.status}</span>`);
});
</script>
@endpush