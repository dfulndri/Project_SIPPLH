@extends('layouts.admin')
@section('title', 'Data Terlapor')
@section('breadcrumb', 'Master Data / Terlapor')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Data Terlapor — {{ \App\Models\Terlapor::$jenisList[$jenis] ?? ucfirst($jenis) }}</h1>
    <p class="page-stl">Kelola data terlapor yang terdaftar dalam sistem</p>
  </div>
  <button class="btn btn-sm btn-maroon" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="bi bi-plus-lg me-1"></i>Tambah Terlapor</button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<ul class="nav nav-tabs mb-3" style="font-size:.84rem">
  @foreach(\App\Models\Terlapor::$jenisList as $key => $label)
  <li class="nav-item">
    <a class="nav-link {{ $jenis == $key ? 'active' : '' }}" href="{{ route('admin.terlapor.index', ['jenis' => $key]) }}">{{ $label }}</a>
  </li>
  @endforeach
</ul>

<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title">{{ $terlapors->total() }} data ditemukan</div>
    <form method="GET" class="d-flex gap-2">
      <input type="hidden" name="jenis" value="{{ $jenis }}">
      <input type="text" name="search" class="form-control form-control-sm" style="width:200px" placeholder="Cari..." value="{{ request('search') }}">
      <button class="btn btn-sm btn-outline-maroon"><i class="bi bi-search"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr>
        <th>Nama</th>
        @if($jenis === 'badan_hukum') <th>Perusahaan</th><th>Bidang Usaha</th><th>NIB</th> @endif
        @if($jenis === 'perorangan') <th>Jenis Usaha</th> @endif
        <th>Alamat</th><th>No. Telp</th><th>Pengaduan</th><th>Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($terlapors as $t)
        <tr>
          <td style="font-size:.84rem"><strong>{{ $t->nama }}</strong></td>
          @if($jenis === 'badan_hukum')
            <td style="font-size:.82rem">{{ $t->nama_perusahaan ?? '—' }}</td>
            <td style="font-size:.82rem">{{ $t->bidang_usaha ?? '—' }}</td>
            <td style="font-size:.82rem">{{ $t->nib ?? '—' }}</td>
          @endif
          @if($jenis === 'perorangan')
            <td style="font-size:.82rem">{{ $t->jenis_usaha ?? '—' }}</td>
          @endif
          <td style="font-size:.82rem;max-width:200px" class="text-truncate">{{ $t->alamat ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $t->no_telp ?? '—' }}</td>
          <td><span class="badge bg-secondary">{{ $t->pengaduan_count }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $t->id }}"><i class="bi bi-pencil"></i></button>
              <form action="{{ route('admin.terlapor.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus data terlapor ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-exclamation-triangle"></i>Belum ada data terlapor</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $terlapors->links() }}</div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form action="{{ route('admin.terlapor.store') }}" method="POST">
      @csrf
      <div class="modal-header"><h6 class="modal-title">Tambah Terlapor</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="jenis_terlapor" value="{{ $jenis }}">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">No. Telp</label><input type="text" name="no_telp" class="form-control"></div>
          @if($jenis === 'badan_hukum')
          <div class="col-md-6"><label class="form-label">Nama Perusahaan</label><input type="text" name="nama_perusahaan" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Bidang Usaha</label><input type="text" name="bidang_usaha" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">NIB</label><input type="text" name="nib" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">NPWP</label><input type="text" name="npwp" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Penanggung Jawab</label><input type="text" name="penanggung_jawab" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">Jabatan PJ</label><input type="text" name="jabatan_pj" class="form-control"></div>
          @endif
          @if($jenis === 'perorangan')
          <div class="col-md-6"><label class="form-label">Jenis Usaha</label><input type="text" name="jenis_usaha" class="form-control"></div>
          @endif
          <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Edit --}}
@foreach($terlapors as $t)
<div class="modal fade" id="editModal{{ $t->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form action="{{ route('admin.terlapor.update', $t) }}" method="POST">
      @csrf @method('PATCH')
      <div class="modal-header"><h6 class="modal-title">Edit Terlapor</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="jenis_terlapor" value="{{ $jenis }}">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control" value="{{ $t->nama }}" required></div>
          <div class="col-md-6"><label class="form-label">No. Telp</label><input type="text" name="no_telp" class="form-control" value="{{ $t->no_telp }}"></div>
          @if($jenis === 'badan_hukum')
          <div class="col-md-6"><label class="form-label">Nama Perusahaan</label><input type="text" name="nama_perusahaan" class="form-control" value="{{ $t->nama_perusahaan }}"></div>
          <div class="col-md-6"><label class="form-label">Bidang Usaha</label><input type="text" name="bidang_usaha" class="form-control" value="{{ $t->bidang_usaha }}"></div>
          <div class="col-md-6"><label class="form-label">NIB</label><input type="text" name="nib" class="form-control" value="{{ $t->nib }}"></div>
          @endif
          <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2">{{ $t->alamat }}</textarea></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>
@endforeach
@endsection
