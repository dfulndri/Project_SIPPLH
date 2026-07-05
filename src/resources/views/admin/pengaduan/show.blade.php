@extends('layouts.admin')
@section('title', 'Detail Pengaduan')
@section('breadcrumb', 'Pengaduan / Detail')

@push('styles')
<style>
.timeline-flow { display:flex; gap:0; margin-bottom:1.5rem; flex-wrap:wrap; }
.tf-step { display:flex; align-items:center; gap:6px; padding:6px 12px; font-size:.72rem; font-weight:500; background:#f3f4f6; color:var(--muted); border-radius:0; position:relative; }
.tf-step:first-child { border-radius:6px 0 0 6px; }
.tf-step:last-child { border-radius:0 6px 6px 0; }
.tf-step.done { background:var(--maroon); color:#fff; }
.tf-step.current { background:#10b981; color:#fff; }
.tf-step i { font-size:.85rem; }
.info-label { font-size:.78rem; color:var(--muted); font-weight:500; margin-bottom:2px; }
.info-value { font-size:.88rem; color:var(--text); margin-bottom:.75rem; }
</style>
@endpush

@section('content')
<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
  <div>
    <h1 class="page-ttl">Detail Pengaduan</h1>
    <p class="page-stl"><code>{{ $pengaduan->nomor_pengaduan }}</code></p>
  </div>
  <div class="d-flex gap-2">
    @if(in_array($pengaduan->status, ['pengaduan_baru','menunggu_disposisi']))
    <a href="{{ route('admin.pengaduan.edit', $pengaduan) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
    <a href="{{ route('admin.disposisi.create', ['pengaduan_id' => $pengaduan->id]) }}" class="btn btn-sm btn-maroon"><i class="bi bi-send me-1"></i>Disposisikan</a>
    @endif
    <a href="{{ route('admin.pengaduan.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.85rem">
  <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Workflow Timeline --}}
@php
$workflow = ['pengaduan_baru','menunggu_disposisi','didisposisikan','verifikasi_lapangan','verifikasi_selesai','tindak_lanjut','selesai','arsip'];
$currentIdx = array_search($pengaduan->status, $workflow);
$icons = ['bi-plus-circle','bi-hourglass','bi-send','bi-clipboard-check','bi-check-circle','bi-arrow-repeat','bi-check-all','bi-archive'];
@endphp
<div class="timeline-flow">
  @foreach($workflow as $i => $step)
  <div class="tf-step {{ $i < $currentIdx ? 'done' : ($i == $currentIdx ? 'current' : '') }}">
    <i class="bi {{ $icons[$i] }}"></i>
    {{ \App\Models\Pengaduan::$statusList[$step] }}
  </div>
  @endforeach
</div>

<div class="row g-3">
  {{-- Info Pengaduan --}}
  <div class="col-md-8">
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-file-earmark-text me-1"></i>Informasi Pengaduan</div></div>
      <div class="cp-body">
        <div class="row">
          <div class="col-md-4">
            <div class="info-label">Tanggal Pengaduan</div>
            <div class="info-value">{{ $pengaduan->tanggal_pengaduan->format('d M Y') }}</div>
          </div>
          <div class="col-md-4">
            <div class="info-label">Sumber Laporan</div>
            <div class="info-value"><span class="badge bg-{{ $pengaduan->sumber_laporan == 'span_lapor' ? 'info' : 'secondary' }}">{{ $pengaduan->sumber_laporan == 'span_lapor' ? 'SPAN LAPOR' : 'Manual' }}</span></div>
          </div>
          <div class="col-md-4">
            <div class="info-label">Status</div>
            <div class="info-value"><span class="badge bg-{{ $pengaduan->status_badge }}">{{ $pengaduan->status_label }}</span></div>
          </div>
          <div class="col-12">
            <div class="info-label">Jenis Aduan</div>
            <div class="info-value">
              @foreach($pengaduan->jenis_aduan_labels as $ja)
                <span class="badge-kat me-1">{{ $ja }}</span>
              @endforeach
            </div>
          </div>
          <div class="col-12">
            <div class="info-label">Uraian Pengaduan</div>
            <div class="info-value" style="white-space:pre-line">{{ $pengaduan->uraian_pengaduan }}</div>
          </div>
          @if($pengaduan->lokasi_kejadian)
          <div class="col-12">
            <div class="info-label">Lokasi Kejadian</div>
            <div class="info-value">{{ $pengaduan->lokasi_kejadian }}</div>
          </div>
          @endif
          <div class="col-md-4">
            <div class="info-label">Kecamatan</div>
            <div class="info-value">{{ $pengaduan->kecamatan?->nama_kecamatan ?? '—' }}</div>
          </div>
          <div class="col-md-4">
            <div class="info-label">Kelurahan</div>
            <div class="info-value">{{ $pengaduan->kelurahan?->nama_kelurahan ?? '—' }}</div>
          </div>
          @if($pengaduan->koordinat_lat)
          <div class="col-md-4">
            <div class="info-label">Koordinat</div>
            <div class="info-value">{{ $pengaduan->koordinat_lat }}, {{ $pengaduan->koordinat_lng }}</div>
          </div>
          @endif
          @if($pengaduan->dokumen_pendukung)
          <div class="col-12">
            <div class="info-label">Dokumen Pendukung</div>
            <div class="info-value"><a href="{{ asset('storage/' . $pengaduan->dokumen_pendukung) }}" target="_blank" class="btn btn-xs btn-outline-maroon"><i class="bi bi-download me-1"></i>Unduh Dokumen</a></div>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Disposisi Info --}}
    @if($pengaduan->disposisi)
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-send-fill me-1"></i>Disposisi</div></div>
      <div class="cp-body">
        <div class="row">
          <div class="col-md-4"><div class="info-label">Pengawas</div><div class="info-value"><strong>{{ $pengaduan->disposisi->pengawas?->name }}</strong></div></div>
          <div class="col-md-4"><div class="info-label">Jadwal Verifikasi</div><div class="info-value">{{ $pengaduan->disposisi->jadwal_verifikasi->format('d M Y') }}</div></div>
          <div class="col-md-4"><div class="info-label">Didisposisikan Oleh</div><div class="info-value">{{ $pengaduan->disposisi->pembuat?->name }}</div></div>
          @if($pengaduan->disposisi->catatan)
          <div class="col-12"><div class="info-label">Catatan</div><div class="info-value">{{ $pengaduan->disposisi->catatan }}</div></div>
          @endif
        </div>
      </div>
    </div>
    @endif

    {{-- Verifikasi Info --}}
    @if($pengaduan->verifikasi)
    <div class="card-panel mb-3">
      <div class="cp-head">
        <div class="cp-title"><i class="bi bi-clipboard-check-fill me-1"></i>Verifikasi Lapangan</div>
        <a href="{{ route('admin.verifikasi.show', $pengaduan->verifikasi) }}" class="btn btn-xs btn-outline-maroon">Detail <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="cp-body">
        <div class="row">
          <div class="col-md-3"><div class="info-label">Tanggal</div><div class="info-value">{{ $pengaduan->verifikasi->tanggal_verifikasi->format('d M Y') }}</div></div>
          <div class="col-md-3"><div class="info-label">Status</div><div class="info-value"><span class="badge bg-{{ $pengaduan->verifikasi->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($pengaduan->verifikasi->status) }}</span></div></div>
          <div class="col-md-3"><div class="info-label">Verifikator</div><div class="info-value">{{ $pengaduan->verifikasi->pembuat?->name }}</div></div>
          @if($pengaduan->verifikasi->beritaAcara)
          <div class="col-md-3"><div class="info-label">Berita Acara</div><div class="info-value"><a href="{{ route('admin.berita-acara.show', $pengaduan->verifikasi->beritaAcara) }}" class="btn btn-xs btn-outline-maroon">{{ $pengaduan->verifikasi->beritaAcara->nomor_ba }}</a></div></div>
          @endif
        </div>
      </div>
    </div>
    @endif

    {{-- Tindak Lanjut --}}
    @if($pengaduan->tindakLanjut->isNotEmpty())
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-arrow-repeat me-1"></i>Tindak Lanjut</div></div>
      <div class="cp-body">
        @foreach($pengaduan->tindakLanjut as $tl)
        <div class="border rounded p-3 mb-2" style="font-size:.84rem">
          <div class="d-flex justify-content-between mb-1">
            <strong>{{ $tl->tanggal->format('d M Y') }}</strong>
            <span class="badge bg-{{ $tl->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($tl->status) }}</span>
          </div>
          <p class="mb-1">{{ $tl->catatan }}</p>
          @if($tl->hasil)<p class="text-muted mb-0"><strong>Hasil:</strong> {{ $tl->hasil }}</p>@endif
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar: Pelapor & Terlapor --}}
  <div class="col-md-4">
    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-person-badge me-1"></i>Pelapor</div></div>
      <div class="cp-body">
        <div class="info-label">Nama</div>
        <div class="info-value">{{ $pengaduan->pelapor?->nama_display }}</div>
        <div class="info-label">Jenis</div>
        <div class="info-value"><span class="badge bg-info text-dark">{{ $pengaduan->pelapor?->jenis_label }}</span></div>
        @if($pengaduan->pelapor?->nik)<div class="info-label">NIK</div><div class="info-value">{{ $pengaduan->pelapor->nik }}</div>@endif
        @if($pengaduan->pelapor?->no_telp)<div class="info-label">No. Telp</div><div class="info-value">{{ $pengaduan->pelapor->no_telp }}</div>@endif
        @if($pengaduan->pelapor?->email)<div class="info-label">Email</div><div class="info-value">{{ $pengaduan->pelapor->email }}</div>@endif
        @if($pengaduan->pelapor?->alamat)<div class="info-label">Alamat</div><div class="info-value">{{ $pengaduan->pelapor->alamat }}</div>@endif
      </div>
    </div>

    <div class="card-panel mb-3">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-exclamation-triangle me-1"></i>Terlapor</div></div>
      <div class="cp-body">
        <div class="info-label">Nama</div>
        <div class="info-value">{{ $pengaduan->terlapor?->nama ?? '—' }}</div>
        <div class="info-label">Jenis</div>
        <div class="info-value"><span class="badge bg-warning text-dark">{{ $pengaduan->terlapor?->jenis_label }}</span></div>
        @if($pengaduan->terlapor?->jenis_usaha)<div class="info-label">Jenis Usaha</div><div class="info-value">{{ $pengaduan->terlapor->jenis_usaha }}</div>@endif
        @if($pengaduan->terlapor?->alamat)<div class="info-label">Alamat</div><div class="info-value">{{ $pengaduan->terlapor->alamat }}</div>@endif
        @if($pengaduan->terlapor?->no_telp)<div class="info-label">No. Telp</div><div class="info-value">{{ $pengaduan->terlapor->no_telp }}</div>@endif
      </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card-panel">
      <div class="cp-head"><div class="cp-title"><i class="bi bi-lightning me-1"></i>Aksi Cepat</div></div>
      <div class="cp-body">
        <form action="{{ route('admin.pengaduan.status', $pengaduan) }}" method="POST">
          @csrf @method('PATCH')
          <div class="mb-2">
            <label class="form-label">Ubah Status</label>
            <select name="status" class="form-select form-select-sm">
              @foreach(\App\Models\Pengaduan::$statusList as $key => $label)
              <option value="{{ $key }}" {{ $pengaduan->status == $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan_admin" class="form-control form-control-sm" rows="2">{{ $pengaduan->catatan_admin }}</textarea>
          </div>
          <button type="submit" class="btn btn-sm btn-maroon w-100"><i class="bi bi-save me-1"></i>Update</button>
        </form>

        <hr>
        @if(!$pengaduan->disposisi && in_array($pengaduan->status, ['pengaduan_baru','menunggu_disposisi']))
        <a href="{{ route('admin.disposisi.create', ['pengaduan_id' => $pengaduan->id]) }}" class="btn btn-sm btn-outline-maroon w-100 mb-2"><i class="bi bi-send me-1"></i>Disposisikan</a>
        @endif
        @if(!$pengaduan->verifikasi && in_array($pengaduan->status, ['didisposisikan']))
        <a href="{{ route('admin.verifikasi.create', ['pengaduan_id' => $pengaduan->id]) }}" class="btn btn-sm btn-outline-maroon w-100 mb-2"><i class="bi bi-clipboard-check me-1"></i>Buat Verifikasi</a>
        @endif
        @if(in_array($pengaduan->status, ['verifikasi_selesai','tindak_lanjut']))
        <a href="{{ route('admin.tindak-lanjut.create', ['pengaduan_id' => $pengaduan->id]) }}" class="btn btn-sm btn-outline-maroon w-100 mb-2"><i class="bi bi-arrow-repeat me-1"></i>Tindak Lanjut</a>
        @endif

        <form action="{{ route('admin.pengaduan.destroy', $pengaduan) }}" method="POST" onsubmit="return confirm('Hapus pengaduan ini secara permanen?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection