@extends('layouts.admin')
@section('title', 'Edit — ' . $pengaduan->nomor_pengaduan)
@section('breadcrumb', 'Pengaduan / Edit')

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Edit Pengaduan</h1>
    <p class="page-stl">Mengubah data pengaduan <code>{{ $pengaduan->nomor_pengaduan }}</code></p>
  </div>
  <a href="{{ route('admin.pengaduan.show', $pengaduan) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="font-size:.85rem">
  <div class="d-flex align-items-center gap-2 mb-1"><i class="bi bi-exclamation-circle-fill"></i><strong>Mohon periksa kembali isian formulir:</strong></div>
  <ul class="mb-0 ps-4">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('admin.pengaduan.update', $pengaduan) }}" enctype="multipart/form-data">
  @csrf @method('PATCH')

  {{-- ══ INFORMASI PENGADUAN ══ --}}
  <div class="card-panel mb-3">
    <div class="cp-head"><div class="cp-title"><i class="bi bi-info-circle me-1"></i>Informasi Pengaduan</div></div>
    <div class="cp-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal Pengaduan <span class="text-danger">*</span></label>
          <input type="date" name="tanggal_pengaduan" class="form-control @error('tanggal_pengaduan') is-invalid @enderror"
                 value="{{ old('tanggal_pengaduan', $pengaduan->tanggal_pengaduan->format('Y-m-d')) }}" required>
          @error('tanggal_pengaduan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Sumber Laporan <span class="text-danger">*</span></label>
          <select name="sumber_laporan" class="form-select @error('sumber_laporan') is-invalid @enderror" required>
            <option value="manual" {{ old('sumber_laporan', $pengaduan->sumber_laporan) == 'manual' ? 'selected' : '' }}>Manual</option>
            <option value="span_lapor" {{ old('sumber_laporan', $pengaduan->sumber_laporan) == 'span_lapor' ? 'selected' : '' }}>SPAN LAPOR</option>
          </select>
          @error('sumber_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Jenis Aduan <span class="text-danger">*</span></label>
          <div class="d-flex flex-wrap gap-3">
            @php $currentJenis = old('jenis_aduan', $pengaduan->jenis_aduan ?? []); @endphp
            @foreach(\App\Models\Pengaduan::$jenisAduanList as $key => $label)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="jenis_aduan[]" value="{{ $key }}" id="ja_{{ $key }}"
                {{ in_array($key, $currentJenis) ? 'checked' : '' }}>
              <label class="form-check-label" for="ja_{{ $key }}" style="font-size:.84rem">{{ $label }}</label>
            </div>
            @endforeach
          </div>
          @error('jenis_aduan')<div class="text-danger" style="font-size:.82rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Uraian Pengaduan <span class="text-danger">*</span></label>
          <textarea name="uraian_pengaduan" class="form-control @error('uraian_pengaduan') is-invalid @enderror" rows="4" required>{{ old('uraian_pengaduan', $pengaduan->uraian_pengaduan) }}</textarea>
          @error('uraian_pengaduan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    {{-- ══ DATA PELAPOR ══ --}}
    <div class="col-md-6">
      <div class="card-panel h-100">
        <div class="cp-head"><div class="cp-title"><i class="bi bi-person-badge me-1"></i>Data Pelapor</div></div>
        <div class="cp-body">
          @php $pl = $pengaduan->pelapor; @endphp
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Pelapor <span class="text-danger">*</span></label>
              <select name="jenis_pelapor" class="form-select" required>
                @foreach(\App\Models\Pelapor::$jenisList as $key => $label)
                <option value="{{ $key }}" {{ old('jenis_pelapor', $pl?->jenis_pelapor) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Pelapor <span class="text-danger">*</span></label>
              <input type="text" name="nama_pelapor" class="form-control @error('nama_pelapor') is-invalid @enderror"
                     value="{{ old('nama_pelapor', $pl?->nama_pelapor) }}" required>
              @error('nama_pelapor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">NIK</label>
              <input type="text" name="nik" class="form-control" value="{{ old('nik', $pl?->nik) }}" maxlength="20">
            </div>
            <div class="col-md-6">
              <label class="form-label">No. Telp</label>
              <input type="text" name="no_telp_pelapor" class="form-control" value="{{ old('no_telp_pelapor', $pl?->no_telp) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email_pelapor" class="form-control" value="{{ old('email_pelapor', $pl?->email) }}">
            </div>
            <div class="col-12">
              <label class="form-label">Alamat</label>
              <textarea name="alamat_pelapor" class="form-control" rows="2">{{ old('alamat_pelapor', $pl?->alamat) }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kecamatan</label>
              <select name="kecamatan_pelapor" class="form-select">
                <option value="">— Pilih —</option>
                @foreach($kecamatans as $k)
                <option value="{{ $k->id }}" {{ old('kecamatan_pelapor', $pl?->kecamatan_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="anonim" value="1" id="anonim" {{ old('anonim', $pl?->anonim) ? 'checked' : '' }}>
                <label class="form-check-label" for="anonim">Pelapor Anonim</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ DATA TERLAPOR ══ --}}
    <div class="col-md-6">
      <div class="card-panel h-100">
        <div class="cp-head"><div class="cp-title"><i class="bi bi-exclamation-triangle me-1"></i>Data Terlapor</div></div>
        <div class="cp-body">
          @php $tr = $pengaduan->terlapor; @endphp
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Terlapor <span class="text-danger">*</span></label>
              <select name="jenis_terlapor" class="form-select" required>
                @foreach(\App\Models\Terlapor::$jenisList as $key => $label)
                <option value="{{ $key }}" {{ old('jenis_terlapor', $tr?->jenis_terlapor) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Terlapor <span class="text-danger">*</span></label>
              <input type="text" name="nama_terlapor" class="form-control @error('nama_terlapor') is-invalid @enderror"
                     value="{{ old('nama_terlapor', $tr?->nama) }}" required>
              @error('nama_terlapor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Jenis Usaha</label>
              <input type="text" name="jenis_usaha" class="form-control" value="{{ old('jenis_usaha', $tr?->jenis_usaha) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">No. Telp</label>
              <input type="text" name="no_telp_terlapor" class="form-control" value="{{ old('no_telp_terlapor', $tr?->no_telp) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Perusahaan</label>
              <input type="text" name="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan', $tr?->nama_perusahaan) }}">
            </div>
            <div class="col-12">
              <label class="form-label">Alamat Terlapor</label>
              <textarea name="alamat_terlapor" class="form-control" rows="2">{{ old('alamat_terlapor', $tr?->alamat) }}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ LOKASI ══ --}}
  <div class="card-panel mb-3">
    <div class="cp-head"><div class="cp-title"><i class="bi bi-geo-alt me-1"></i>Lokasi Kejadian</div></div>
    <div class="cp-body">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Alamat Lokasi</label>
          <textarea name="lokasi_kejadian" class="form-control" rows="2">{{ old('lokasi_kejadian', $pengaduan->lokasi_kejadian) }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Kecamatan</label>
          <select name="kecamatan_id" class="form-select kec-select">
            <option value="">— Pilih —</option>
            @foreach($kecamatans as $k)
            <option value="{{ $k->id }}" {{ old('kecamatan_id', $pengaduan->kecamatan_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Kelurahan</label>
          <select name="kelurahan_id" class="form-select" id="kelurahanSelect">
            <option value="">— Pilih kecamatan dulu —</option>
            @if($pengaduan->kelurahan)
            <option value="{{ $pengaduan->kelurahan_id }}" selected>{{ $pengaduan->kelurahan->nama_kelurahan }}</option>
            @endif
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Latitude</label>
          <input type="number" name="koordinat_lat" class="form-control" step="any" value="{{ old('koordinat_lat', $pengaduan->koordinat_lat) }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Longitude</label>
          <input type="number" name="koordinat_lng" class="form-control" step="any" value="{{ old('koordinat_lng', $pengaduan->koordinat_lng) }}">
        </div>
      </div>
    </div>
  </div>

  {{-- ══ DOKUMEN ══ --}}
  <div class="card-panel mb-3">
    <div class="cp-head"><div class="cp-title"><i class="bi bi-paperclip me-1"></i>Dokumen Pendukung</div></div>
    <div class="cp-body">
      @if($pengaduan->dokumen_pendukung)
      <div class="mb-2">
        <a href="{{ asset('storage/' . $pengaduan->dokumen_pendukung) }}" target="_blank" class="btn btn-xs btn-outline-maroon"><i class="bi bi-download me-1"></i>Dokumen saat ini</a>
      </div>
      @endif
      <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
      @error('dokumen_pendukung')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <small class="text-muted">Kosongkan jika tidak ingin mengganti. Format: PDF, JPG, PNG. Maks 5MB.</small>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-maroon"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
    <a href="{{ route('admin.pengaduan.show', $pengaduan) }}" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
@endsection

@push('scripts')
<script>
document.querySelector('.kec-select')?.addEventListener('change', function() {
  const kelSelect = document.getElementById('kelurahanSelect');
  kelSelect.innerHTML = '<option value="">Memuat...</option>';
  if (!this.value) { kelSelect.innerHTML = '<option value="">— Pilih kecamatan dulu —</option>'; return; }
  fetch(`/admin/master/kelurahan-json/${this.value}`)
    .then(r => r.json())
    .then(data => {
      kelSelect.innerHTML = '<option value="">— Pilih —</option>';
      data.forEach(k => { kelSelect.innerHTML += `<option value="${k.id}">${k.nama_kelurahan}</option>`; });
    });
});
</script>
@endpush