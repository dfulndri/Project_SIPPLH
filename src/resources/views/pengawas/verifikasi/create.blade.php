@extends('layouts.pengawas')
@section('title','Buat Verifikasi')
@section('breadcrumb','Buat Verifikasi')

@section('content')

<div class="page-hd d-flex align-items-center justify-content-between">
  <div>
    <h1 class="page-ttl">Buat Verifikasi Lapangan</h1>
    <p class="page-stl">Formulir laporan hasil verifikasi di lapangan.</p>
  </div>
  <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-sm btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Kembali
  </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" style="font-size:.85rem">
  <i class="bi bi-exclamation-circle-fill me-1"></i>
  <ul class="mb-0 ps-4 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Info Pengaduan --}}
<div class="card-panel mb-3" style="border-left:3px solid var(--maroon)">
  <div class="cp-body py-2">
    <div class="d-flex align-items-center gap-3">
      <i class="bi bi-file-text" style="color:var(--maroon);font-size:1.2rem"></i>
      <div>
        <div style="font-size:.78rem;color:var(--muted)">Pengaduan yang diverifikasi</div>
        <div>
          <code>{{ $pengaduan->nomor_pengaduan }}</code>
          <span class="ms-2" style="font-size:.85rem">— {{ $pengaduan->terlapor?->nama }}</span>
          <span class="ms-1 badge-kat">{{ ucwords(str_replace('_',' ',$pengaduan->kategori)) }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

<form method="POST" action="{{ route('pengawas.verifikasi.store') }}" enctype="multipart/form-data">
@csrf
<input type="hidden" name="pengaduan_id" value="{{ $pengaduan->id }}">

{{-- Tanggal & Tenggat --}}
<div class="card-panel mb-3">
  <div class="cp-head">
    <div class="d-flex align-items-center gap-2">
      <div style="width:28px;height:28px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">0</div>
      <div class="cp-title">Informasi Umum</div>
    </div>
  </div>
  <div class="cp-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Tanggal Verifikasi <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_verifikasi" class="form-control"
          value="{{ old('tanggal_verifikasi', now()->format('Y-m-d')) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Tenggat Tindak Lanjut</label>
        <input type="date" name="tenggat_tindak_lanjut" class="form-control"
          value="{{ old('tenggat_tindak_lanjut', now()->addDays(14)->format('Y-m-d')) }}">
        <div class="form-text">Default 14 hari</div>
      </div>
    </div>
  </div>
</div>

{{-- Tim Verifikator --}}
<div class="card-panel mb-3">
  <div class="cp-head">
    <div class="d-flex align-items-center gap-2">
      <div style="width:28px;height:28px;background:var(--maroon);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">A</div>
      <div class="cp-title">Tim Verifikator</div>
    </div>
    <button type="button" class="btn btn-sm btn-outline-maroon" onclick="addTim()">
      <i class="bi bi-plus-circle me-1"></i> Tambah
    </button>
  </div>
  <div class="cp-body">
    <div class="row g-2 mb-1 d-none d-md-flex">
      @foreach(['Nama *','NIP','Pangkat','Jabatan',''] as $h)
      <div class="{{ $h==='' ? 'col-md-1' : ($h==='Nama *'?'col-md-3':'col-md-2') }}">
        <span style="font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase">{{ $h }}</span>
      </div>
      @endforeach
    </div>
    <div id="tim-container">
      <div class="tim-row mb-2">
        <div class="row g-2 align-items-center">
          <div class="col-12 col-md-3">
            <input type="text" name="tim[0][nama]" class="form-control form-control-sm"
              placeholder="Nama lengkap" value="{{ old('tim.0.nama', auth()->user()->name) }}">
          </div>
          <div class="col-6 col-md-2">
            <input type="text" name="tim[0][nip]" class="form-control form-control-sm"
              placeholder="NIP" value="{{ old('tim.0.nip', auth()->user()->nip) }}">
          </div>
          <div class="col-6 col-md-2">
            <input type="text" name="tim[0][pangkat]" class="form-control form-control-sm" placeholder="III/a">
          </div>
          <div class="col-10 col-md-3">
            <input type="text" name="tim[0][jabatan]" class="form-control form-control-sm"
              placeholder="Jabatan" value="{{ old('tim.0.jabatan', auth()->user()->jabatan) }}">
          </div>
          <div class="col-2 col-md-1 text-end">
            <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTim(this)">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    @if($pegawai->isNotEmpty())
    <div class="mt-2 pt-2 border-top">
      <div style="font-size:.78rem;color:var(--muted);margin-bottom:4px">Isi cepat dari data pegawai:</div>
      <div class="d-flex flex-wrap gap-1">
        @foreach($pegawai as $pg)
        <button type="button" class="btn btn-xs btn-outline-secondary"
          onclick="addTimFill('{{ $pg->name }}','{{ $pg->nip }}','','{{ $pg->jabatan }}')">
          {{ $pg->name }}
        </button>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>

{{-- Penanggung Jawab --}}
<div class="card-panel mb-3">
  <div class="cp-head">
    <div class="d-flex align-items-center gap-2">
      <div style="width:28px;height:28px;background:var(--maroon-md);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">B</div>
      <div class="cp-title">Penanggung Jawab Usaha / Kegiatan</div>
    </div>
  </div>
  <div class="cp-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama PJ</label>
        <input type="text" name="pj_nama_pj" class="form-control" value="{{ old('pj_nama_pj') }}" placeholder="Nama penanggung jawab">
      </div>
      <div class="col-md-6">
        <label class="form-label">Jabatan PJ</label>
        <input type="text" name="pj_jabatan_pj" class="form-control" value="{{ old('pj_jabatan_pj') }}" placeholder="Direktur / Manajer">
      </div>
      <div class="col-md-6">
        <label class="form-label">Nama Perusahaan</label>
        <input type="text" name="pj_nama_perusahaan" class="form-control"
          value="{{ old('pj_nama_perusahaan', $pengaduan->terlapor?->nama) }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Bidang Usaha</label>
        <input type="text" name="pj_bidang_usaha" class="form-control"
          value="{{ old('pj_bidang_usaha', $pengaduan->terlapor?->jenis_usaha) }}">
      </div>
      <div class="col-md-3"><label class="form-label">KBLI</label>
        <input type="text" name="pj_kbli" class="form-control" value="{{ old('pj_kbli') }}">
      </div>
      <div class="col-md-3"><label class="form-label">NIB</label>
        <input type="text" name="pj_nib" class="form-control" value="{{ old('pj_nib') }}">
      </div>
      <div class="col-md-3"><label class="form-label">Telepon</label>
        <input type="text" name="pj_no_telp" class="form-control" value="{{ old('pj_no_telp') }}">
      </div>
      <div class="col-md-3"><label class="form-label">Email</label>
        <input type="email" name="pj_email" class="form-control" value="{{ old('pj_email') }}">
      </div>
      <div class="col-12">
        <label class="form-label">Alamat Perusahaan</label>
        <textarea name="pj_alamat" class="form-control" rows="2">{{ old('pj_alamat', $pengaduan->terlapor?->alamat) }}</textarea>
      </div>
    </div>
  </div>
</div>

{{-- Temuan C/D/E --}}
<div class="card-panel mb-3">
  <div class="cp-head">
    <div class="d-flex align-items-center gap-2">
      <div style="width:28px;height:28px;background:#10b981;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;flex-shrink:0">C</div>
      <div class="cp-title">Temuan & Kesimpulan</div>
    </div>
  </div>
  <div class="cp-body">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label"><span class="badge me-1" style="background:var(--maroon)">C</span> Informasi Administrasi</label>
        <textarea name="informasi_administrasi" class="form-control" rows="4"
          placeholder="Status perizinan, dokumen lingkungan, dll...">{{ old('informasi_administrasi') }}</textarea>
      </div>
      <div class="col-12">
        <label class="form-label"><span class="badge me-1" style="background:var(--maroon-md)">D</span> Fakta Temuan Lapangan</label>
        <textarea name="fakta_temuan" class="form-control" rows="5"
          placeholder="Uraikan fakta yang ditemukan di lapangan...">{{ old('fakta_temuan') }}</textarea>
      </div>
      <div class="col-12">
        <label class="form-label"><span class="badge me-1" style="background:#10b981">E</span> Saran Tindak Lanjut</label>
        <textarea name="saran_tindak_lanjut" class="form-control" rows="4"
          placeholder="Rekomendasi dan langkah yang harus dilakukan...">{{ old('saran_tindak_lanjut') }}</textarea>
      </div>
    </div>
  </div>
</div>

{{-- Foto --}}
<div class="card-panel mb-3">
  <div class="cp-head">
    <div class="cp-title"><i class="bi bi-camera me-1 text-muted"></i> Dokumentasi Foto</div>
  </div>
  <div class="cp-body">
    <div id="foto-list">
      <div class="foto-item mb-2 p-2 rounded" style="border:1px dashed var(--border)">
        <div class="row g-2 align-items-center">
          <div class="col-md-5">
            <input type="file" name="foto[]" class="form-control form-control-sm"
              accept="image/*" onchange="previewFoto(this)">
          </div>
          <div class="col-md-6">
            <input type="text" name="foto_keterangan[]" class="form-control form-control-sm" placeholder="Keterangan foto">
          </div>
          <div class="col-md-1 text-end">
            <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeFoto(this)">
              <i class="bi bi-trash"></i>
            </button>
          </div>
          <div class="col-12">
            <img src="" class="foto-preview d-none" style="max-height:80px;border-radius:4px">
          </div>
        </div>
      </div>
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addFoto()">
      <i class="bi bi-plus-circle me-1"></i> Tambah Foto
    </button>
  </div>
</div>

<div class="d-flex justify-content-between">
  <a href="{{ route('pengawas.tugas.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-x-circle me-1"></i> Batal
  </a>
  <button type="submit" class="btn btn-maroon px-5">
    <i class="bi bi-save me-1"></i> Simpan Verifikasi
  </button>
</div>

</form>
@endsection

@push('scripts')
<script>
let timIdx = 1;
function addTim(){
  const i=timIdx++;
  const r=document.createElement('div'); r.className='tim-row mb-2';
  r.innerHTML=`<div class="row g-2 align-items-center">
    <div class="col-12 col-md-3"><input type="text" name="tim[${i}][nama]" class="form-control form-control-sm" placeholder="Nama"></div>
    <div class="col-6 col-md-2"><input type="text" name="tim[${i}][nip]" class="form-control form-control-sm" placeholder="NIP"></div>
    <div class="col-6 col-md-2"><input type="text" name="tim[${i}][pangkat]" class="form-control form-control-sm" placeholder="III/a"></div>
    <div class="col-10 col-md-3"><input type="text" name="tim[${i}][jabatan]" class="form-control form-control-sm" placeholder="Jabatan"></div>
    <div class="col-2 col-md-1 text-end"><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeTim(this)"><i class="bi bi-trash"></i></button></div>
  </div>`;
  document.getElementById('tim-container').appendChild(r);
}
function addTimFill(nama,nip,pangkat,jabatan){
  addTim();
  const rows=document.querySelectorAll('.tim-row'), last=rows[rows.length-1];
  last.querySelector('[name*="[nama]"]').value=nama;
  last.querySelector('[name*="[nip]"]').value=nip;
  last.querySelector('[name*="[pangkat]"]').value=pangkat;
  last.querySelector('[name*="[jabatan]"]').value=jabatan;
}
function removeTim(btn){ if(document.querySelectorAll('.tim-row').length>1) btn.closest('.tim-row').remove(); }
function addFoto(){
  const t=document.querySelector('.foto-item').cloneNode(true);
  t.querySelector('input[type=file]').value=''; t.querySelector('input[type=text]').value='';
  const p=t.querySelector('.foto-preview'); p.src=''; p.classList.add('d-none');
  document.getElementById('foto-list').appendChild(t);
}
function removeFoto(btn){ if(document.querySelectorAll('.foto-item').length>1) btn.closest('.foto-item').remove(); }
function previewFoto(inp){
  const p=inp.closest('.foto-item').querySelector('.foto-preview');
  if(inp.files[0]){const r=new FileReader();r.onload=e=>{p.src=e.target.result;p.classList.remove('d-none')};r.readAsDataURL(inp.files[0]);}
}
</script>
@endpush