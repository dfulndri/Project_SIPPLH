@extends('layouts.admin')
@section('title','Master Data')
@section('breadcrumb','Master Data')

@section('content')
<div class="page-hd">
  <h1 class="page-ttl">Master Data</h1>
  <p class="page-stl">Kelola data referensi kecamatan dan kelurahan/desa.</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i> {{ session('error') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3" id="masterTab">
  <li class="nav-item">
    <a class="nav-link {{ !request()->is('*#kelurahan') ? 'active' : '' }}"
       href="#kecamatan" onclick="switchTab('kecamatan')" id="tab-kecamatan">
      <i class="bi bi-geo-alt me-1"></i> Kecamatan ({{ $kecamatans->count() }})
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#kelurahan" onclick="switchTab('kelurahan')" id="tab-kelurahan">
      <i class="bi bi-pin-map me-1"></i> Kelurahan / Desa ({{ $kelurahans->total() }})
    </a>
  </li>
</ul>

{{-- ═══ TAB: KECAMATAN ═══════════════════════════════════════════ --}}
<div id="pane-kecamatan">
  <div class="row g-3">
    {{-- Form Tambah --}}
    <div class="col-12 col-lg-4">
      <div class="card-panel">
        <div class="cp-head"><div class="cp-title">Tambah Kecamatan</div></div>
        <div class="cp-body">
          <form method="POST" action="{{ route('admin.master.kecamatan.store') }}">
            @csrf
            <div class="mb-2">
              <label class="form-label" style="font-size:.82rem">Nama Kecamatan <span class="text-danger">*</span></label>
              <input type="text" name="nama_kecamatan" class="form-control form-control-sm"
                placeholder="Contoh: Tigaraksa" required>
            </div>
            <div class="mb-3">
              <label class="form-label" style="font-size:.82rem">Kode Kecamatan</label>
              <input type="text" name="kode_kecamatan" class="form-control form-control-sm" placeholder="Kode">
            </div>
            <button type="submit" class="btn btn-sm btn-maroon w-100">
              <i class="bi bi-plus-circle me-1"></i> Tambah
            </button>
          </form>
        </div>
      </div>
    </div>
    {{-- List --}}
    <div class="col-12 col-lg-8">
      <div class="card-panel">
        <div class="cp-head"><div class="cp-title">Daftar Kecamatan</div></div>
        <div class="table-responsive">
          <table class="table sipplh-table">
            <thead>
              <tr><th>#</th><th>Nama Kecamatan</th><th>Kode</th><th>Jml Kelurahan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
              @forelse($kecamatans as $i => $kec)
              <tr>
                <td style="font-size:.75rem;color:var(--muted)">{{ $i+1 }}</td>
                <td><strong style="font-size:.85rem">{{ $kec->nama_kecamatan }}</strong></td>
                <td><code style="font-size:.78rem">{{ $kec->kode_kecamatan ?: '—' }}</code></td>
                <td>
                  <span class="badge" style="background:rgba(106,0,0,.08);color:var(--maroon)">
                    {{ $kec->kelurahan_count }} kel.
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <button class="btn btn-xs btn-outline-warning"
                      onclick="editKec({{ $kec->id }},'{{ $kec->nama_kecamatan }}','{{ $kec->kode_kecamatan }}')"
                      title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.master.kecamatan.destroy',$kec) }}" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus"
                        onclick="return confirm('Hapus kecamatan {{ $kec->nama_kecamatan }}?')">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr><td colspan="5"><div class="empty-state"><i class="bi bi-geo-alt"></i> Belum ada kecamatan</div></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══ TAB: KELURAHAN ════════════════════════════════════════════ --}}
<div id="pane-kelurahan" style="display:none">
  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card-panel">
        <div class="cp-head"><div class="cp-title">Tambah Kelurahan / Desa</div></div>
        <div class="cp-body">
          <form method="POST" action="{{ route('admin.master.kelurahan.store') }}">
            @csrf
            <div class="mb-2">
              <label class="form-label" style="font-size:.82rem">Kecamatan <span class="text-danger">*</span></label>
              <select name="kecamatan_id" class="form-select form-select-sm" required>
                <option value="">-- Pilih --</option>
                @foreach($kecamatans as $kec)
                <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label" style="font-size:.82rem">Nama Kelurahan / Desa <span class="text-danger">*</span></label>
              <input type="text" name="nama_kelurahan" class="form-control form-control-sm"
                placeholder="Nama kelurahan" required>
            </div>
            <div class="mb-3">
              <label class="form-label" style="font-size:.82rem">Kode Kelurahan</label>
              <input type="text" name="kode_kelurahan" class="form-control form-control-sm">
            </div>
            <button type="submit" class="btn btn-sm btn-maroon w-100">
              <i class="bi bi-plus-circle me-1"></i> Tambah
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-8">
      <div class="card-panel">
        <div class="cp-head">
          <div class="cp-title">Daftar Kelurahan / Desa</div>
          <form method="GET" class="d-flex gap-1">
            <input type="hidden" name="_tab" value="kelurahan">
            <select name="kec_filter" class="form-select form-select-sm" style="max-width:160px">
              <option value="">Semua Kecamatan</option>
              @foreach($kecamatans as $kec)
              <option value="{{ $kec->id }}" {{ request('kec_filter')==$kec->id ? 'selected':'' }}>
                {{ $kec->nama_kecamatan }}
              </option>
              @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-maroon">Filter</button>
          </form>
        </div>
        <div class="table-responsive">
          <table class="table sipplh-table">
            <thead>
              <tr><th>#</th><th>Nama Kelurahan</th><th>Kecamatan</th><th>Kode</th><th>Aksi</th></tr>
            </thead>
            <tbody>
              @forelse($kelurahans as $kel)
              <tr>
                <td style="font-size:.75rem;color:var(--muted)">{{ $kelurahans->firstItem()+$loop->index }}</td>
                <td style="font-size:.85rem"><strong>{{ $kel->nama_kelurahan }}</strong></td>
                <td style="font-size:.82rem">{{ $kel->kecamatan?->nama_kecamatan ?? '—' }}</td>
                <td><code style="font-size:.78rem">{{ $kel->kode_kelurahan ?: '—' }}</code></td>
                <td>
                  <form method="POST" action="{{ route('admin.master.kelurahan.destroy',$kel) }}" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Hapus"
                      onclick="return confirm('Hapus {{ $kel->nama_kelurahan }}?')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="5"><div class="empty-state"><i class="bi bi-pin-map"></i> Belum ada kelurahan</div></td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($kelurahans->hasPages())
        <div class="p-3 border-top">{{ $kelurahans->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Modal Edit Kecamatan --}}
<div class="modal fade" id="editKecModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title">Edit Kecamatan</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="editKecForm">
        @csrf @method('PATCH')
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label" style="font-size:.82rem">Nama Kecamatan</label>
            <input type="text" name="nama_kecamatan" id="editKecNama" class="form-control form-control-sm">
          </div>
          <div class="mb-2">
            <label class="form-label" style="font-size:.82rem">Kode</label>
            <input type="text" name="kode_kecamatan" id="editKecKode" class="form-control form-control-sm">
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-maroon">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
function switchTab(tab) {
  document.getElementById('pane-kecamatan').style.display = tab==='kecamatan' ? '' : 'none';
  document.getElementById('pane-kelurahan').style.display = tab==='kelurahan' ? '' : 'none';
  document.getElementById('tab-kecamatan').classList.toggle('active', tab==='kecamatan');
  document.getElementById('tab-kelurahan').classList.toggle('active', tab==='kelurahan');
}

function editKec(id, nama, kode) {
  document.getElementById('editKecForm').action = `/admin/master-data/kecamatan/${id}`;
  document.getElementById('editKecNama').value = nama;
  document.getElementById('editKecKode').value = kode;
  new bootstrap.Modal(document.getElementById('editKecModal')).show();
}

// Auto switch tab if URL hash
document.addEventListener('DOMContentLoaded', () => {
  if(location.hash === '#kelurahan') switchTab('kelurahan');
});
</script>
@endpush