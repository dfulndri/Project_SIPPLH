@extends('layouts.admin')
@section('title', 'Manajemen Wilayah')
@section('breadcrumb', 'Manajemen Wilayah')

@section('content')
<div class="page-hd mb-3">
  <h1 class="page-ttl">Manajemen Wilayah</h1>
  <p class="page-stl">Kelola data kecamatan dan kelurahan</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<ul class="nav nav-tabs mb-3" id="wilayahTab" role="tablist" style="font-size:.84rem">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#kecamatanTab">Kecamatan ({{ $kecamatans->count() }})</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#kelurahanTab">Kelurahan</a></li>
</ul>

<div class="tab-content">
  {{-- Tab Kecamatan --}}
  <div class="tab-pane fade show active" id="kecamatanTab">
    <div class="card-panel">
      <div class="cp-head">
        <div class="cp-title">Daftar Kecamatan</div>
        <button class="btn btn-sm btn-maroon" data-bs-toggle="modal" data-bs-target="#addKecModal"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
      </div>
      <div class="table-responsive">
        <table class="table sipplh-table">
          <thead><tr><th>Nama Kecamatan</th><th>Kode</th><th>Kelurahan</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @foreach($kecamatans as $kec)
            <tr>
              <td><strong>{{ $kec->nama_kecamatan }}</strong></td>
              <td style="font-size:.82rem">{{ $kec->kode_kecamatan ?? '—' }}</td>
              <td><span class="badge bg-secondary">{{ $kec->kelurahan_count }}</span></td>
              <td><span class="badge bg-{{ $kec->is_active ? 'success' : 'secondary' }}">{{ $kec->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
              <td>
                <div class="d-flex gap-1">
                  <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editKecModal{{ $kec->id }}"><i class="bi bi-pencil"></i></button>
                  <form action="{{ route('admin.wilayah.kecamatan.destroy', $kec) }}" method="POST" onsubmit="return confirm('Hapus kecamatan ini beserta kelurahannya?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Tab Kelurahan --}}
  <div class="tab-pane fade" id="kelurahanTab">
    <div class="card-panel">
      <div class="cp-head">
        <div class="cp-title">Daftar Kelurahan</div>
        <button class="btn btn-sm btn-maroon" data-bs-toggle="modal" data-bs-target="#addKelModal"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
      </div>
      <div class="table-responsive">
        <table class="table sipplh-table">
          <thead><tr><th>Nama Kelurahan</th><th>Kecamatan</th><th>Kode</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @foreach($kelurahans as $kel)
            <tr>
              <td><strong>{{ $kel->nama_kelurahan }}</strong></td>
              <td style="font-size:.82rem">{{ $kel->kecamatan?->nama_kecamatan }}</td>
              <td style="font-size:.82rem">{{ $kel->kode_kelurahan ?? '—' }}</td>
              <td><span class="badge bg-{{ $kel->is_active ? 'success' : 'secondary' }}">{{ $kel->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
              <td>
                <div class="d-flex gap-1">
                  <button class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editKelModal{{ $kel->id }}"><i class="bi bi-pencil"></i></button>
                  <form action="{{ route('admin.wilayah.kelurahan.destroy', $kel) }}" method="POST" onsubmit="return confirm('Hapus kelurahan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $kelurahans->links() }}</div>
    </div>
  </div>
</div>

{{-- Modal Tambah Kecamatan --}}
<div class="modal fade" id="addKecModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.wilayah.kecamatan.store') }}" method="POST">
      @csrf
      <div class="modal-header"><h6 class="modal-title">Tambah Kecamatan</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Nama Kecamatan <span class="text-danger">*</span></label><input type="text" name="nama_kecamatan" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Kode</label><input type="text" name="kode_kecamatan" class="form-control" maxlength="10"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>

{{-- Modal Tambah Kelurahan --}}
<div class="modal fade" id="addKelModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.wilayah.kelurahan.store') }}" method="POST">
      @csrf
      <div class="modal-header"><h6 class="modal-title">Tambah Kelurahan</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
          <select name="kecamatan_id" class="form-select" required>
            <option value="">— Pilih —</option>
            @foreach($kecamatans as $kec)<option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>@endforeach
          </select>
        </div>
        <div class="mb-3"><label class="form-label">Nama Kelurahan <span class="text-danger">*</span></label><input type="text" name="nama_kelurahan" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Kode</label><input type="text" name="kode_kelurahan" class="form-control" maxlength="10"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>

{{-- Edit Modals --}}
@foreach($kecamatans as $kec)
<div class="modal fade" id="editKecModal{{ $kec->id }}" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.wilayah.kecamatan.update', $kec) }}" method="POST">
      @csrf @method('PATCH')
      <div class="modal-header"><h6 class="modal-title">Edit Kecamatan</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama_kecamatan" class="form-control" value="{{ $kec->nama_kecamatan }}" required></div>
        <div class="mb-3"><label class="form-label">Kode</label><input type="text" name="kode_kecamatan" class="form-control" value="{{ $kec->kode_kecamatan }}"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>
@endforeach
@foreach($kelurahans as $kel)
<div class="modal fade" id="editKelModal{{ $kel->id }}" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form action="{{ route('admin.wilayah.kelurahan.update', $kel) }}" method="POST">
      @csrf @method('PATCH')
      <div class="modal-header"><h6 class="modal-title">Edit Kelurahan</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Kecamatan</label>
          <select name="kecamatan_id" class="form-select">
            @foreach($kecamatans as $k)<option value="{{ $k->id }}" {{ $kel->kecamatan_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>@endforeach
          </select>
        </div>
        <div class="mb-3"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama_kelurahan" class="form-control" value="{{ $kel->nama_kelurahan }}" required></div>
        <div class="mb-3"><label class="form-label">Kode</label><input type="text" name="kode_kelurahan" class="form-control" value="{{ $kel->kode_kelurahan }}"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-maroon">Simpan</button></div>
    </form>
  </div></div>
</div>
@endforeach
@endsection
