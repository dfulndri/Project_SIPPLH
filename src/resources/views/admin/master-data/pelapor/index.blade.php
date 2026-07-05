@extends('layouts.admin')
@section('title', 'Data Pelapor')
@section('breadcrumb', 'Master Data / Pelapor')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Data Pelapor — {{ \App\Models\Pelapor::$jenisList[$jenis] ?? ucfirst($jenis) }}</h1>
    <p class="page-stl">Kelola data pelapor yang terdaftar dalam sistem</p>
  </div>
  <button class="btn btn-sm btn-maroon" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="bi bi-plus-lg me-1"></i>Tambah Pelapor</button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tab Jenis --}}
<ul class="nav nav-tabs mb-3" style="font-size:.84rem">
  @foreach(\App\Models\Pelapor::$jenisList as $key => $label)
  <li class="nav-item">
    <a class="nav-link {{ $jenis == $key ? 'active' : '' }}" href="{{ route('admin.pelapor.index', ['jenis' => $key]) }}">
      {{ $label }}
    </a>
  </li>
  @endforeach
</ul>

<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title">{{ $pelapors->total() }} data ditemukan</div>
    <form method="GET" class="d-flex gap-2">
      <input type="hidden" name="jenis" value="{{ $jenis }}">
      <input type="text" name="search" class="form-control form-control-sm" style="width:200px" placeholder="Cari nama, NIK, telp..." value="{{ request('search') }}">
      <button class="btn btn-sm btn-outline-maroon"><i class="bi bi-search"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead><tr>
        <th>Nama</th>
        <th>NIK</th>
        <th>No. Telp</th>
        <th>Email</th>
        <th>Kecamatan</th>
        <th>Pengaduan</th>
        <th>Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($pelapors as $p)
        <tr>
          <td style="font-size:.84rem">
            <strong>{{ $p->nama_pelapor }}</strong>
            @if($p->anonim) <span class="badge bg-secondary" style="font-size:.62rem">Anonim</span> @endif
            @if($p->nama_lembaga) <br><small class="text-muted">{{ $p->nama_lembaga }}</small> @endif
          </td>
          <td style="font-size:.82rem">{{ $p->nik ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->no_telp ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->email ?? '—' }}</td>
          <td style="font-size:.82rem">{{ $p->kecamatan?->nama_kecamatan ?? '—' }}</td>
          <td><span class="badge bg-secondary">{{ $p->pengaduan_count }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $p->id }}"><i class="bi bi-pencil"></i></button>
              <form action="{{ route('admin.pelapor.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus data pelapor ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="empty-state"><i class="bi bi-person-badge"></i>Belum ada data pelapor</div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $pelapors->links() }}</div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form action="{{ route('admin.pelapor.store') }}" method="POST">
      @csrf
      <div class="modal-header"><h6 class="modal-title">Tambah Pelapor</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="jenis_pelapor" value="{{ $jenis }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Pelapor <span class="text-danger">*</span></label>
            <input type="text" name="nama_pelapor" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">NIK</label>
            <input type="text" name="nik" class="form-control" maxlength="20">
          </div>
          <div class="col-md-6">
            <label class="form-label">No. Telp</label>
            <input type="text" name="no_telp" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Kecamatan</label>
            <select name="kecamatan_id" class="form-select kec-select">
              <option value="">— Pilih —</option>
              @foreach($kecamatans as $kec)
                <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
              @endforeach
            </select>
          </div>
          @if($jenis !== 'perorangan')
          <div class="col-md-6">
            <label class="form-label">Nama Lembaga/Perusahaan</label>
            <input type="text" name="nama_lembaga" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan_di_lembaga" class="form-control">
          </div>
          @endif
          @if($jenis === 'badan_hukum')
          <div class="col-md-6">
            <label class="form-label">NPWP</label>
            <input type="text" name="npwp" class="form-control" maxlength="25">
          </div>
          @endif
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>

{{-- Modals Edit --}}
@foreach($pelapors as $p)
<div class="modal fade" id="editModal{{ $p->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form action="{{ route('admin.pelapor.update', $p) }}" method="POST">
      @csrf @method('PATCH')
      <div class="modal-header"><h6 class="modal-title">Edit Pelapor</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="jenis_pelapor" value="{{ $jenis }}">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama_pelapor" class="form-control" value="{{ $p->nama_pelapor }}" required></div>
          <div class="col-md-6"><label class="form-label">NIK</label><input type="text" name="nik" class="form-control" value="{{ $p->nik }}"></div>
          <div class="col-md-6"><label class="form-label">No. Telp</label><input type="text" name="no_telp" class="form-control" value="{{ $p->no_telp }}"></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $p->email }}"></div>
          <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2">{{ $p->alamat }}</textarea></div>
          @if($jenis !== 'perorangan')
          <div class="col-md-6"><label class="form-label">Nama Lembaga</label><input type="text" name="nama_lembaga" class="form-control" value="{{ $p->nama_lembaga }}"></div>
          <div class="col-md-6"><label class="form-label">Jabatan</label><input type="text" name="jabatan_di_lembaga" class="form-control" value="{{ $p->jabatan_di_lembaga }}"></div>
          @endif
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>
@endforeach
@endsection
