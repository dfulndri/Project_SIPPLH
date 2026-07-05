@extends('layouts.admin')
@section('title', 'Buat Disposisi')
@section('breadcrumb', 'Disposisi / Buat Baru')

@section('content')
<div class="page-hd mb-3">
  <h1 class="page-ttl">Buat Disposisi Pengaduan</h1>
  <p class="page-stl">Disposisikan pengaduan ke pengawas lapangan untuk verifikasi</p>
</div>

<div class="card-panel">
  <div class="cp-head"><div class="cp-title">Form Disposisi</div></div>
  <div class="cp-body">
    <form action="{{ route('admin.disposisi.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Pilih Pengaduan <span class="text-danger">*</span></label>
        <select name="pengaduan_id" class="form-select @error('pengaduan_id') is-invalid @enderror" required>
          <option value="">— Pilih pengaduan —</option>
          @foreach($pengaduanList as $pg)
            <option value="{{ $pg->id }}" {{ ($pengaduan?->id == $pg->id || old('pengaduan_id') == $pg->id) ? 'selected' : '' }}>
              {{ $pg->nomor_pengaduan }} — {{ $pg->terlapor?->nama ?? 'N/A' }}
            </option>
          @endforeach
        </select>
        @error('pengaduan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Pengawas Lapangan <span class="text-danger">*</span></label>
          <select name="pengawas_id" class="form-select @error('pengawas_id') is-invalid @enderror" required>
            <option value="">— Pilih pengawas —</option>
            @foreach($pengawas as $p)
              <option value="{{ $p->id }}" {{ old('pengawas_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->nip ?? '-' }})</option>
            @endforeach
          </select>
          @error('pengawas_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Jadwal Verifikasi <span class="text-danger">*</span></label>
          <input type="date" name="jadwal_verifikasi" class="form-control @error('jadwal_verifikasi') is-invalid @enderror" value="{{ old('jadwal_verifikasi') }}" required>
          @error('jadwal_verifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Catatan Disposisi</label>
        <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan atau arahan khusus...">{{ old('catatan') }}</textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-maroon"><i class="bi bi-send-fill me-1"></i>Disposisikan</button>
        <a href="{{ route('admin.disposisi.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
