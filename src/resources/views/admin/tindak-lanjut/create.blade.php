@extends('layouts.admin')
@section('title', 'Tambah Tindak Lanjut')
@section('breadcrumb', 'Tindak Lanjut / Tambah')

@section('content')
<div class="page-hd mb-3">
  <h1 class="page-ttl">Tambah Tindak Lanjut</h1>
  <p class="page-stl">Catat tindakan lanjutan untuk pengaduan yang telah diverifikasi</p>
</div>

<div class="card-panel">
  <div class="cp-head"><div class="cp-title">Form Tindak Lanjut</div></div>
  <div class="cp-body">
    <form action="{{ route('admin.tindak-lanjut.store') }}" method="POST" enctype="multipart/form-data">
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
          <label class="form-label">Tanggal <span class="text-danger">*</span></label>
          <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->toDateString()) }}" required>
          @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Dokumen Pendukung</label>
          <input type="file" name="dokumen" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
          @error('dokumen')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Catatan Tindak Lanjut <span class="text-danger">*</span></label>
        <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="4" required placeholder="Uraian tindakan lanjutan yang dilakukan...">{{ old('catatan') }}</textarea>
        @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="mb-3">
        <label class="form-label">Hasil</label>
        <textarea name="hasil" class="form-control" rows="3" placeholder="Hasil dari tindak lanjut (opsional)...">{{ old('hasil') }}</textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-maroon"><i class="bi bi-save me-1"></i>Simpan</button>
        <a href="{{ route('admin.tindak-lanjut.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
