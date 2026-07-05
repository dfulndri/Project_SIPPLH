@extends('layouts.admin')
@section('title', 'Laporan')
@section('breadcrumb', 'Laporan')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Laporan Pengaduan</h1>
    <p class="page-stl">Rekap dan analisis data pengaduan lingkungan hidup</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.laporan.pdf', request()->all()) }}" class="btn btn-sm btn-outline-maroon" target="_blank"><i class="bi bi-filetype-pdf me-1"></i>Export PDF</a>
    <a href="{{ route('admin.laporan.excel', request()->all()) }}" class="btn btn-sm btn-outline-maroon"><i class="bi bi-filetype-xlsx me-1"></i>Export Excel</a>
  </div>
</div>

{{-- Filter --}}
<div class="card-panel mb-3">
  <div class="cp-body py-3">
    <form method="GET" action="{{ route('admin.laporan.index') }}">
      <div class="row g-2 align-items-end">
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Dari</label>
          <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Sampai</label>
          <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">Semua</option>
            @foreach(\App\Models\Pengaduan::$statusList as $key => $label)
            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Kecamatan</label>
          <select name="kecamatan_id" class="form-select form-select-sm">
            <option value="">Semua</option>
            @foreach($kecamatans as $k)
            <option value="{{ $k->id }}" {{ request('kecamatan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-maroon flex-fill"><i class="bi bi-funnel me-1"></i>Filter</button>
          <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-outline-secondary flex-fill"><i class="bi bi-x-circle me-1"></i>Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
  @foreach([
    ['Total', $stats['total'], 'bi-folder2-open', 'var(--maroon)'],
    ['Baru', $stats['baru'], 'bi-plus-circle', '#94a3b8'],
    ['Dalam Proses', $stats['proses'], 'bi-hourglass-split', '#f59e0b'],
    ['Verifikasi Selesai', $stats['verifikasi'], 'bi-check-circle', '#10b981'],
    ['Selesai / Arsip', $stats['selesai'], 'bi-check-all', '#22c55e'],
  ] as [$lbl, $val, $ico, $clr])
  <div class="col-6 col-md">
    <div class="stat-card" style="border-top-color:{{ $clr }}">
      <div><div class="sc-num">{{ $val }}</div><div class="sc-lbl">{{ $lbl }}</div></div>
      <i class="bi {{ $ico }} sc-ic" style="color:{{ $clr }}"></i>
    </div>
  </div>
  @endforeach
</div>

{{-- Charts --}}
<div class="row g-3 mb-4">
  <div class="col-md-7">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title">Sebaran Jenis Aduan</div></div>
      <div class="cp-body"><div style="height:220px"><canvas id="jenisChart"></canvas></div></div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card-panel h-100">
      <div class="cp-head"><div class="cp-title">Top Kecamatan</div></div>
      <div class="cp-body">
        @php $maxKec = $perKecamatan->max('total') ?: 1; @endphp
        @forelse($perKecamatan as $pk)
        <div class="mb-2">
          <div class="d-flex justify-content-between mb-1" style="font-size:.8rem">
            <span>{{ $pk->kecamatan?->nama_kecamatan ?? '—' }}</span><strong>{{ $pk->total }}</strong>
          </div>
          <div style="height:8px;border-radius:4px;background:rgba(106,0,0,.08);overflow:hidden">
            <div style="height:100%;width:{{ round(($pk->total/$maxKec)*100) }}%;background:var(--maroon);border-radius:4px;transition:width .6s"></div>
          </div>
        </div>
        @empty
        <div class="text-muted" style="font-size:.82rem">Belum ada data</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- Tabel --}}
<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title">Data Pengaduan ({{ $pengaduans->total() }})</div>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr><th>#</th><th>Nomor</th><th>Tanggal</th><th>Pelapor</th><th>Terlapor</th><th>Jenis</th><th>Kecamatan</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($pengaduans as $p)
        <tr>
          <td class="text-muted" style="font-size:.75rem">{{ $pengaduans->firstItem() + $loop->index }}</td>
          <td><code style="font-size:.78rem">{{ $p->nomor_pengaduan }}</code></td>
          <td style="font-size:.82rem">{{ $p->tanggal_pengaduan->format('d M Y') }}</td>
          <td style="font-size:.82rem">{{ $p->pelapor?->nama_display ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->terlapor?->nama ?? '—' }}</td>
          <td>@foreach($p->jenis_aduan_labels as $ja)<span class="badge-kat">{{ $ja }}</span>@endforeach</td>
          <td style="font-size:.82rem">{{ $p->kecamatan?->nama_kecamatan ?? '—' }}</td>
          <td><span class="badge bg-{{ $p->status_badge }}">{{ $p->status_label }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-bar-chart"></i>Tidak ada data sesuai filter</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $pengaduans->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
const jenisLabels = @json(array_map(fn($k) => \App\Models\Pengaduan::$jenisAduanList[$k] ?? ucwords(str_replace('_',' ',$k)), array_keys($perJenis)));
const jenisData = @json(array_values($perJenis));
new Chart(document.getElementById('jenisChart'), {
  type:'bar', data:{labels:jenisLabels, datasets:[{data:jenisData,backgroundColor:['#6A0000','#935656','#3b82f6','#f59e0b','#10b981','#94a3b8'],borderRadius:4}]},
  options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,.04)'}},y:{ticks:{font:{size:10}},grid:{display:false}}}}
});
</script>
@endpush