@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  #map { height: 380px; border-radius: 8px; z-index: 0; }
  .stat-card-sm { background:#fff; border-radius:10px; padding:.9rem 1rem; box-shadow:var(--shadow); border-top:3px solid; height:100% }
  .sc-num-sm { font-size:1.6rem; font-weight:700; line-height:1; color:var(--text) }
  .sc-lbl-sm { font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-top:4px }
  .kec-bar { height:8px; border-radius:4px; background:rgba(106,0,0,.1); overflow:hidden }
  .kec-fill { height:100%; border-radius:4px; background:var(--maroon); transition:width .6s }
  .status-flow { display:flex; gap:4px; flex-wrap:wrap; }
  .sf-item { font-size:.65rem; padding:3px 8px; border-radius:12px; background:#f3f4f6; color:var(--muted); font-weight:500; }
  .sf-item.active { background:var(--maroon); color:#fff; }
</style>
@endpush

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1 class="page-ttl">Dashboard</h1>
    <p class="page-stl">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Ringkasan data SIPPLH.</p>
  </div>
  <div style="font-size:.78rem;color:var(--muted)" class="d-none d-md-block">
    <i class="bi bi-calendar3 me-1"></i>{{ now()->isoFormat('dddd, D MMMM Y') }}
  </div>
</div>

{{-- ══ STAT CARDS ══ --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card-sm" style="border-top-color:var(--maroon)">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="sc-num-sm">{{ $stats['total_pengaduan'] }}</div>
          <div class="sc-lbl-sm">Total Pengaduan</div>
        </div>
        <i class="bi bi-folder2-open" style="font-size:1.6rem;opacity:.1;color:var(--maroon)"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card-sm" style="border-top-color:#3b82f6">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="sc-num-sm">{{ $stats['total_verifikasi'] }}</div>
          <div class="sc-lbl-sm">Total Verifikasi</div>
        </div>
        <i class="bi bi-clipboard-check-fill" style="font-size:1.6rem;opacity:.1;color:#3b82f6"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card-sm" style="border-top-color:#10b981">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="sc-num-sm">{{ $stats['total_pengawas'] }}</div>
          <div class="sc-lbl-sm">Total Pengawas</div>
        </div>
        <i class="bi bi-people-fill" style="font-size:1.6rem;opacity:.1;color:#10b981"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card-sm" style="border-top-color:#f59e0b">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="sc-num-sm">{{ $stats['total_tahun'] }}</div>
          <div class="sc-lbl-sm">Pengaduan {{ now()->year }}</div>
        </div>
        <i class="bi bi-calendar-check-fill" style="font-size:1.6rem;opacity:.1;color:#f59e0b"></i>
      </div>
    </div>
  </div>
</div>

{{-- ══ TREN + STATUS ══ --}}
<div class="row g-3 mb-4">
  <div class="col-12 col-xl-8">
    <div class="card-panel">
      <div class="cp-head">
        <div>
          <div class="cp-title">Tren Pengaduan Bulanan</div>
          <div class="cp-sub">Jumlah pengaduan per bulan</div>
        </div>
        <select id="tahunSelect" class="form-select form-select-sm" style="width:100px">
          @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="cp-body"><div class="chart-wrap"><canvas id="trendChart"></canvas></div></div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="card-panel">
      <div class="cp-head"><div class="cp-title">Status Pengaduan</div></div>
      <div class="cp-body"><div class="chart-wrap"><canvas id="statusChart"></canvas></div></div>
    </div>
  </div>
</div>

{{-- ══ MAP ══ --}}
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="card-panel">
      <div class="cp-head">
        <div>
          <div class="cp-title"><i class="bi bi-geo-alt-fill me-1" style="color:var(--maroon)"></i>Sebaran Wilayah Pengaduan</div>
          <div class="cp-sub">{{ count($mapData) }} titik koordinat terdaftar</div>
        </div>
      </div>
      <div class="cp-body p-0">
        <div id="map"></div>
        @if(empty($mapData))
        <div class="p-3 text-center text-muted" style="font-size:.82rem">
          <i class="bi bi-geo-alt d-block fs-3 mb-1 opacity-25"></i>Belum ada data dengan titik koordinat.
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ══ KATEGORI + TOP KECAMATAN ══ --}}
<div class="row g-3 mb-4">
  <div class="col-12 col-md-7">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title">Jenis Aduan Pencemaran</div></div>
      <div class="cp-body"><div style="height:220px"><canvas id="kategoriChart"></canvas></div></div>
    </div>
  </div>
  <div class="col-12 col-md-5">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title">Top Kecamatan</div></div>
      <div class="cp-body">
        @php $maxKec = $perKecamatan->max('total') ?: 1; @endphp
        @forelse($perKecamatan->take(5) as $pk)
        <div class="mb-2">
          <div class="d-flex justify-content-between mb-1" style="font-size:.8rem">
            <span>{{ $pk->kecamatan?->nama_kecamatan ?? 'N/A' }}</span>
            <strong>{{ $pk->total }}</strong>
          </div>
          <div class="kec-bar"><div class="kec-fill" style="width:{{ round(($pk->total/$maxKec)*100) }}%"></div></div>
        </div>
        @empty
        <div class="text-muted" style="font-size:.82rem">Belum ada data</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- ══ TABEL TERBARU ══ --}}
<div class="card-panel">
  <div class="cp-head">
    <div>
      <div class="cp-title">Pengaduan Terbaru</div>
      <div class="cp-sub">5 pengaduan terakhir masuk</div>
    </div>
    <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-sm btn-outline-maroon">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr><th>Nomor</th><th>Tanggal</th><th>Pelapor</th><th>Terlapor</th><th>Jenis Aduan</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($recentPengaduan as $p)
        <tr>
          <td><code style="font-size:.78rem">{{ $p->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $p->tanggal_pengaduan->format('d M Y') }}</td>
          <td style="font-size:.82rem">{{ $p->pelapor?->nama_display ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->terlapor?->nama ?? '—' }}</td>
          <td>
            @foreach($p->jenis_aduan_labels as $ja)
              <span class="badge-kat">{{ $ja }}</span>
            @endforeach
          </td>
          <td><span class="badge bg-{{ $p->status_badge }}">{{ $p->status_label }}</span></td>
          <td><a href="{{ route('admin.pengaduan.show', $p) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada pengaduan masuk</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

// Tren Chart
let trendChart = new Chart(document.getElementById('trendChart'), {
  type:'line',
  data:{labels:months, datasets:[{label:'Pengaduan',data:@json($chartData),borderColor:'#6A0000',backgroundColor:'rgba(106,0,0,.07)',borderWidth:2,fill:true,tension:.4,pointBackgroundColor:'#6A0000',pointBorderColor:'#fff',pointBorderWidth:2,pointRadius:5,pointHoverRadius:7}]},
  options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,.04)'}},x:{grid:{display:false}}}}
});
document.getElementById('tahunSelect').addEventListener('change', function(){
  fetch(`/admin/dashboard/chart/${this.value}`).then(r=>r.json()).then(data=>{trendChart.data.datasets[0].data=data;trendChart.update();});
});

// Status Doughnut
const statusLabels = @json(array_values(\App\Models\Pengaduan::$statusList));
const statusData = @json(array_values($statusCounts));
new Chart(document.getElementById('statusChart'), {
  type:'doughnut',
  data:{labels:statusLabels, datasets:[{data:statusData, backgroundColor:['#94a3b8','#f59e0b','#06b6d4','#3b82f6','#10b981','#1e293b','#22c55e','#64748b'], borderWidth:0, hoverOffset:8}]},
  options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{position:'bottom',labels:{padding:10,font:{size:10},usePointStyle:true}}}}
});

// Kategori Chart
new Chart(document.getElementById('kategoriChart'), {
  type:'bar',
  data:{labels:@json(array_values($jenisLabels)), datasets:[{data:@json(array_values($jenisCount)),backgroundColor:['#6A0000','#935656','#3b82f6','#f59e0b','#10b981','#94a3b8'],borderRadius:4}]},
  options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,.04)'}},y:{ticks:{font:{size:10}},grid:{display:false}}}}
});

// Leaflet Map
const map = L.map('map').setView([-6.2, 106.63], 10);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap', maxZoom:18}).addTo(map);

const statusColors = {
  pengaduan_baru:'#94a3b8', menunggu_disposisi:'#f59e0b', didisposisikan:'#06b6d4',
  verifikasi_lapangan:'#3b82f6', verifikasi_selesai:'#10b981', tindak_lanjut:'#1e293b',
  selesai:'#22c55e', arsip:'#64748b'
};

const mapData = @json($mapData);
mapData.forEach(p => {
  const c = statusColors[p.status_key] || '#6A0000';
  const icon = L.divIcon({className:'',html:`<div style="width:14px;height:14px;border-radius:50%;background:${c};border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.4)"></div>`,iconSize:[14,14],iconAnchor:[7,7]});
  L.marker([p.lat, p.lng], {icon})
    .addTo(map)
    .bindPopup(`<div style="min-width:200px">
      <strong>${p.nomor}</strong><br>
      <small style="color:#666"><i class="bi bi-person"></i> ${p.pelapor}</small><br>
      <small style="color:#666"><i class="bi bi-building"></i> ${p.terlapor}</small><br>
      <small><i class="bi bi-tag"></i> ${p.jenis}</small><br>
      <span class="badge" style="background:${c};color:#fff;font-size:10px;margin-top:4px">${p.status}</span>
      <br><small style="color:#999">${p.tanggal}</small>
    </div>`);
});

const legend = L.control({position:'bottomright'});
legend.onAdd = () => {
  const d = L.DomUtil.create('div');
  d.innerHTML = `<div style="background:#fff;padding:8px 10px;border-radius:6px;box-shadow:0 1px 6px rgba(0,0,0,.2);font-size:10px">
    <div style="font-weight:600;margin-bottom:4px">Status</div>
    ${Object.entries(statusColors).map(([k,c])=>`<div><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${c};margin-right:4px"></span>${k.replace(/_/g,' ')}</div>`).join('')}
  </div>`;
  return d;
};
legend.addTo(map);
</script>
@endpush
