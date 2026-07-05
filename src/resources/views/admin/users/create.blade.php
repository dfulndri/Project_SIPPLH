@extends('layouts.admin')
@section('title','Tambah User')
@section('breadcrumb','Tambah User')

@section('content')

<div class="page-hd d-flex align-items-center justify-content-between">
  <div>
    <h1 class="page-ttl">Tambah Pengguna</h1>
    <p class="page-stl">Daftarkan akun pengguna baru ke sistem SIPPLH.</p>
  </div>
  <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Kembali
  </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="font-size:.85rem">
  <i class="bi bi-exclamation-circle-fill me-1"></i>
  <strong>Periksa kembali isian:</strong>
  <ul class="mb-0 mt-1 ps-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 justify-content-center">
  <div class="col-12 col-lg-8">
    <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div class="card-panel mb-3">
      <div class="cp-head">
        <div class="cp-title">Informasi Akun</div>
        <div class="cp-sub">Kredensial untuk login ke sistem</div>
      </div>
      <div class="cp-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name') }}" placeholder="Nama lengkap pengguna">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email') }}" placeholder="email@sipplh.go.id">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="password" name="password" id="passInput"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Min. 8 karakter, huruf besar + angka">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                <i class="bi bi-eye" id="passIcon"></i>
              </button>
            </div>
            @error('password')<div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Role / Hak Akses <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role') is-invalid @enderror">
              @foreach([
                'admin'    => ['Admin — Akses penuh ke seluruh sistem','var(--maroon)'],
                'pengawas' => ['Pengawas — Akses verifikasi lapangan','var(--maroon-md)'],
                'viewer'   => ['Viewer — Hanya dapat melihat data','#64748b'],
              ] as $v => [$lbl,$col])
              <option value="{{ $v }}" {{ old('role') == $v ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card-panel mb-3">
      <div class="cp-head">
        <div class="cp-title">Informasi Pegawai</div>
        <div class="cp-sub">Data identitas pegawai (opsional)</div>
      </div>
      <div class="cp-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">NIP (Nomor Induk Pegawai)</label>
            <input type="text" name="nip" class="form-control"
              value="{{ old('nip') }}" placeholder="18 digit NIP">
          </div>
          <div class="col-md-6">
            <label class="form-label">No. Telepon / HP</label>
            <input type="text" name="no_telp" class="form-control"
              value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx">
          </div>
          <div class="col-12">
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan" class="form-control"
              value="{{ old('jabatan') }}" placeholder="Contoh: Pengawas Lingkungan Hidup Muda">
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
              <label class="form-check-label" for="isActive" style="font-size:.85rem">
                <i class="bi bi-check-circle me-1 text-success"></i> Akun aktif (bisa login)
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> Batal
      </a>
      <button type="submit" class="btn btn-maroon px-5">
        <i class="bi bi-person-check me-1"></i> Buat Akun
      </button>
    </div>

    </form>
  </div>
</div>

@endsection
@push('scripts')
<script>
function togglePass(){
  const inp = document.getElementById('passInput');
  const ico = document.getElementById('passIcon');
  inp.type = inp.type === 'password' ? 'text' : 'password';
  ico.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush