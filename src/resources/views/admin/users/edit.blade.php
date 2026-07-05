@extends('layouts.admin')
@section('title','Edit User — ' . $user->name)
@section('breadcrumb','Edit User')

@section('content')

<div class="page-hd d-flex align-items-center justify-content-between">
  <div>
    <h1 class="page-ttl">Edit Pengguna</h1>
    <p class="page-stl">Memperbarui data akun <strong>{{ $user->name }}</strong></p>
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
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf @method('PATCH')

    <div class="card-panel mb-3">
      <div class="cp-head">
        <div class="cp-title">Informasi Akun</div>
      </div>
      <div class="cp-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $user->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email', $user->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Password Baru
              <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
            </label>
            <div class="input-group">
              <input type="password" name="password" id="passInput"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Password baru...">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePass()">
                <i class="bi bi-eye" id="passIcon"></i>
              </button>
            </div>
            @error('password')<div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role') is-invalid @enderror"
              {{ $user->id === auth()->id() ? 'disabled' : '' }}>
              @foreach(['admin'=>'Admin','pengawas'=>'Pengawas','viewer'=>'Viewer'] as $v => $l)
              <option value="{{ $v }}" {{ old('role', $user->role) == $v ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
            @if($user->id === auth()->id())
            <div class="form-text">Role tidak dapat diubah untuk akun yang sedang aktif.</div>
            <input type="hidden" name="role" value="{{ $user->role }}">
            @endif
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title">Informasi Pegawai</div></div>
      <div class="cp-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">NIP</label>
            <input type="text" name="nip" class="form-control"
              value="{{ old('nip', $user->nip) }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="no_telp" class="form-control"
              value="{{ old('no_telp', $user->no_telp) }}">
          </div>
          <div class="col-12">
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan" class="form-control"
              value="{{ old('jabatan', $user->jabatan) }}">
          </div>
          @if($user->id !== auth()->id())
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_active"
                id="isActive" value="1"
                {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="isActive" style="font-size:.85rem">
                <i class="bi bi-check-circle me-1 text-success"></i> Akun aktif (bisa login)
              </label>
            </div>
          </div>
          @else
          <input type="hidden" name="is_active" value="1">
          @endif
        </div>
      </div>
    </div>

    {{-- Info --}}
    <div class="card-panel mb-3">
      <div class="cp-body" style="font-size:.75rem;color:var(--muted);line-height:2">
        <div><strong>ID:</strong> #{{ $user->id }}</div>
        <div><strong>Dibuat:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</div>
        <div><strong>Terakhir update:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</div>
      </div>
    </div>

    <div class="d-flex justify-content-between">
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i> Batal
      </a>
      <button type="submit" class="btn btn-maroon px-5">
        <i class="bi bi-save me-1"></i> Simpan Perubahan
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