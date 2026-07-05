@extends('layouts.admin')
@section('title', 'Tambah Pengaduan')
@section('breadcrumb', 'Pengaduan / Tambah')

@section('content')
<div class="page-hd mb-3">
  <h1 class="page-ttl">Tambah Pengaduan Baru</h1>
  <p class="page-stl">Input data pengaduan lingkungan hidup</p>
</div>

<form action="{{ route('admin.pengaduan.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  {{-- ══ SUMBER & JENIS ADUAN ══ --}}
  <div class="card-panel mb-3">
    <div class="cp-head"><div class="cp-title"><i class="bi bi-info-circle me-1"></i>Informasi Pengaduan</div></div>
    <div class="cp-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal Pengaduan <span class="text-danger">*</span></label>
          <input type="date" name="tanggal_pengaduan" class="form-control @error('tanggal_pengaduan') is-invalid @enderror" value="{{ old('tanggal_pengaduan', now()->toDateString()) }}" required>
          @error('tanggal_pengaduan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label">Sumber Laporan <span class="text-danger">*</span></label>
          <select name="sumber_laporan" class="form-select @error('sumber_laporan') is-invalid @enderror" required>
            <option value="manual" {{ old('sumber_laporan') == 'manual' ? 'selected' : '' }}>Manual</option>
            <option value="span_lapor" {{ old('sumber_laporan') == 'span_lapor' ? 'selected' : '' }}>SPAN LAPOR</option>
          </select>
          @error('sumber_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Jenis Aduan <span class="text-danger">*</span></label>
          <div class="d-flex flex-wrap gap-3">
            @foreach(\App\Models\Pengaduan::$jenisAduanList as $key => $label)
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="jenis_aduan[]" value="{{ $key }}" id="ja_{{ $key }}"
                {{ in_array($key, old('jenis_aduan', [])) ? 'checked' : '' }}>
              <label class="form-check-label" for="ja_{{ $key }}" style="font-size:.84rem">{{ $label }}</label>
            </div>
            @endforeach
          </div>
          @error('jenis_aduan')<div class="text-danger" style="font-size:.82rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Uraian Pengaduan <span class="text-danger">*</span></label>
          <textarea name="uraian_pengaduan" class="form-control @error('uraian_pengaduan') is-invalid @enderror" rows="4" required placeholder="Jelaskan kronologi pengaduan secara detail (min. 20 karakter)...">{{ old('uraian_pengaduan') }}</textarea>
          @error('uraian_pengaduan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    {{-- ══ PELAPOR ══ --}}
    <div class="col-md-6">
      <div class="card-panel h-100">
        <div class="cp-head"><div class="cp-title"><i class="bi bi-person-badge me-1"></i>Data Pelapor</div></div>
        <div class="cp-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Pelapor <span class="text-danger">*</span></label>
              <select name="jenis_pelapor" class="form-select" id="jenisPelapor" required>
                @foreach(\App\Models\Pelapor::$jenisList as $key => $label)
                <option value="{{ $key }}" {{ old('jenis_pelapor') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Pelapor <span class="text-danger">*</span></label>
              <input type="text" name="nama_pelapor" class="form-control @error('nama_pelapor') is-invalid @enderror" value="{{ old('nama_pelapor') }}" required>
              @error('nama_pelapor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">NIK</label>
              <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" maxlength="20">
            </div>
            <div class="col-md-6">
              <label class="form-label">No. Telp</label>
              <input type="text" name="no_telp_pelapor" class="form-control" value="{{ old('no_telp_pelapor') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email_pelapor" class="form-control" value="{{ old('email_pelapor') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Alamat</label>
              <textarea name="alamat_pelapor" class="form-control" rows="2">{{ old('alamat_pelapor') }}</textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kecamatan</label>
              <select name="kecamatan_pelapor" class="form-select kec-select-pelapor">
                <option value="">— Pilih —</option>
                @foreach($kecamatans as $k)
                <option value="{{ $k->id }}" {{ old('kecamatan_pelapor') == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" name="anonim" value="1" id="anonim" {{ old('anonim') ? 'checked' : '' }}>
                <label class="form-check-label" for="anonim">Pelapor Anonim</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ TERLAPOR ══ --}}
    <div class="col-md-6">
      <div class="card-panel h-100">
        <div class="cp-head"><div class="cp-title"><i class="bi bi-exclamation-triangle me-1"></i>Data Terlapor</div></div>
        <div class="cp-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Jenis Terlapor <span class="text-danger">*</span></label>
              <select name="jenis_terlapor" class="form-select" required>
                @foreach(\App\Models\Terlapor::$jenisList as $key => $label)
                <option value="{{ $key }}" {{ old('jenis_terlapor') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nama Terlapor <span class="text-danger">*</span></label>
              <input type="text" name="nama_terlapor" class="form-control @error('nama_terlapor') is-invalid @enderror" value="{{ old('nama_terlapor') }}" required>
              @error('nama_terlapor')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Jenis Usaha</label>
              <input type="text" name="jenis_usaha" class="form-control" value="{{ old('jenis_usaha') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">No. Telp</label>
              <input type="text" name="no_telp_terlapor" class="form-control" value="{{ old('no_telp_terlapor') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Alamat Terlapor</label>
              <textarea name="alamat_terlapor" class="form-control" rows="2">{{ old('alamat_terlapor') }}</textarea>
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
          <label class="form-label">Alamat Lokasi Kejadian</label>
          <textarea name="lokasi_kejadian" class="form-control" rows="2" placeholder="Deskripsi lokasi kejadian...">{{ old('lokasi_kejadian') }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Kecamatan</label>
          <select name="kecamatan_id" class="form-select kec-select">
            <option value="">— Pilih —</option>
            @foreach($kecamatans as $k)
            <option value="{{ $k->id }}" {{ old('kecamatan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kecamatan }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Kelurahan</label>
          <select name="kelurahan_id" class="form-select" id="kelurahanSelect">
            <option value="">— Pilih kecamatan dulu —</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Latitude</label>
          <input type="number" name="koordinat_lat" class="form-control" step="any" value="{{ old('koordinat_lat') }}" placeholder="-6.xxxx">
        </div>
        <div class="col-md-2">
          <label class="form-label">Longitude</label>
          <input type="number" name="koordinat_lng" class="form-control" step="any" value="{{ old('koordinat_lng') }}" placeholder="106.xxxx">
        </div>
      </div>
    </div>
  </div>

  {{-- ══ DOKUMEN ══ --}}
  <div class="card-panel mb-3">
    <div class="cp-head"><div class="cp-title"><i class="bi bi-paperclip me-1"></i>Dokumen Pendukung</div></div>
    <div class="cp-body">
      <input type="file" name="dokumen_pendukung" class="form-control @error('dokumen_pendukung') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
      @error('dokumen_pendukung')<div class="invalid-feedback">{{ $message }}</div>@enderror
      <small class="text-muted">Format: PDF, JPG, PNG. Maks 5MB.</small>
    </div>
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-maroon"><i class="bi bi-save me-1"></i>Simpan Pengaduan</button>
    <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
@endsection

@push('scripts')
<script>
// Dynamic kelurahan loading
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
