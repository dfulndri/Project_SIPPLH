@extends('layouts.pengawas')
@section('title','Profil Saya')
@section('breadcrumb','Profil Saya')

@section('content')

<div class="page-hd"><h1 class="page-ttl">Profil Saya</h1><p class="page-stl">Perbarui informasi dan keamanan akun Anda.</p></div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3 justify-content-center">
  <div class="col-12 col-lg-7">
    <form method="POST" action="{{ route('pengawas.profil.update') }}">
    @csrf @method('PATCH')

    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title">Informasi Pegawai</div></div>
      <div class="cp-body">
        {{-- Avatar --}}
        <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded" style="background:var(--mint-bg)">
          <div style="width:60px;height:60px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;flex-shrink:0">
            {{ strtoupper(substr($user->name,0,1)) }}
          </div>
          <div>
            <div style="font-size:1rem;font-weight:600">{{ $user->name }}</div>
            <div style="font-size:.82rem;color:var(--muted)">{{ $user->email }}</div>
            <span class="badge mt-1" style="background:var(--maroon);font-size:.72rem">{{ ucfirst($user->role) }}</span>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $user->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">No. Telepon / HP</label>
            <input type="text" name="no_telp" class="form-control"
              value="{{ old('no_telp', $user->no_telp) }}" placeholder="08xxxxxxxxxx">
          </div>
          <div class="col-12">
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan" class="form-control"
              value="{{ old('jabatan', $user->jabatan) }}" placeholder="Jabatan resmi Anda">
          </div>
          <div class="col-md-6">
            <label class="form-label">NIP</label>
            <input type="text" class="form-control" value="{{ $user->nip ?? '—' }}" disabled>
            <div class="form-text">NIP hanya dapat diubah oleh Administrator.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
          </div>
        </div>
      </div>
    </div>

    <div class="card-panel mb-3">
      <div class="cp-head">
        <div class="cp-title">Ubah Password</div>
        <div class="cp-sub">Kosongkan jika tidak ingin mengubah password</div>
      </div>
      <div class="cp-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Password Lama</label>
            <input type="password" name="old_password"
              class="form-control @error('old_password') is-invalid @enderror"
              placeholder="Masukkan password saat ini">
            @error('old_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password"
              class="form-control @error('password') is-invalid @enderror"
              placeholder="Min. 8 karakter">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control"
              placeholder="Ulangi password baru">
          </div>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-maroon px-5">
      <i class="bi bi-save me-1"></i> Simpan Perubahan
    </button>

    </form>
  </div>
</div>

@endsection