<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Berita Acara {{ $ba->nomor_ba }}</title>
<style>
  @page { margin: 2cm 2.5cm; }
  body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6; color: #000; }
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .text-justify { text-align: justify; }
  .fw-bold { font-weight: bold; }
  .mb-0 { margin-bottom: 0; }
  .mb-1 { margin-bottom: 5px; }
  .mb-2 { margin-bottom: 10px; }
  .mb-3 { margin-bottom: 15px; }
  .mt-2 { margin-top: 10px; }
  .mt-3 { margin-top: 15px; }

  /* KOP Surat */
  .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
  .kop-table td { vertical-align: middle; padding: 0; }
  .kop-logo { width: 80px; }
  .kop-text { text-align: center; padding-left: 10px; }
  .kop-text h2 { font-size: 13pt; margin: 0; letter-spacing: 1px; }
  .kop-text h1 { font-size: 15pt; margin: 0; letter-spacing: 2px; }
  .kop-text p { font-size: 9pt; margin: 2px 0 0; }
  .kop-line { border-top: 3px solid #000; border-bottom: 1px solid #000; height: 5px; margin: 8px 0 15px; }

  /* Judul BA */
  .ba-title { text-align: center; margin: 15px 0 20px; }
  .ba-title h3 { font-size: 12pt; text-decoration: underline; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
  .ba-title p { font-size: 11pt; margin: 5px 0 0; }

  /* Section headers */
  .section-header { font-weight: bold; font-size: 11pt; margin: 15px 0 8px; text-decoration: underline; }

  /* Numbered list */
  .info-list { margin: 0; padding: 0; }
  .info-list li { list-style: none; margin-bottom: 3px; font-size: 11pt; }
  .info-list .label { display: inline-block; width: 200px; }

  /* Text box for sections */
  .text-box { border: 1px solid #000; padding: 8px 10px; min-height: 60px; font-size: 11pt; text-align: justify; margin-bottom: 10px; }

  /* Signature table */
  .sig-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
  .sig-table td { width: 33.33%; text-align: center; vertical-align: top; padding: 5px; font-size: 10pt; }
  .sig-space { height: 70px; }

  /* Photo grid */
  .foto-grid { width: 100%; border-collapse: collapse; }
  .foto-grid td { width: 33.33%; padding: 5px; text-align: center; vertical-align: top; }
  .foto-grid img { max-width: 100%; max-height: 160px; border: 1px solid #ccc; }
  .foto-caption { font-size: 9pt; margin-top: 3px; color: #555; }

  /* QR */
  .qr-section { margin-top: 20px; text-align: right; }
  .qr-section img { width: 100px; }
  .qr-label { font-size: 8pt; color: #666; }

  .page-break { page-break-before: always; }
</style>
</head>
<body>

{{-- ══ KOP SURAT ══ --}}
<table class="kop-table">
  <tr>
    <td class="kop-logo">
      @if($logoPath)
        <img src="{{ $logoPath }}" alt="Logo" style="width:75px">
      @endif
    </td>
    <td class="kop-text">
      <h2>PEMERINTAH {{ strtoupper($profil->nama_kabupaten ?? 'KABUPATEN TANGERANG') }}</h2>
      <h1>{{ strtoupper($profil->nama_instansi ?? 'DINAS LINGKUNGAN HIDUP DAN KEBERSIHAN') }}</h1>
      <p>{{ $profil->alamat ?? '' }}</p>
      <p>Telp: {{ $profil->telepon ?? '' }} | Email: {{ $profil->email ?? '' }}</p>
    </td>
  </tr>
</table>
<div class="kop-line"></div>

{{-- ══ JUDUL ══ --}}
<div class="ba-title">
  <h3>BERITA ACARA<br>HASIL VERIFIKASI LAPANGAN PENGADUAN<br>DUGAAN PENCEMARAN DAN/ATAU PERUSAKAN LINGKUNGAN HIDUP</h3>
  <p>Nomor: {{ $ba->nomor_ba }}</p>
</div>

{{-- ══ PARAGRAF PEMBUKA ══ --}}
@php
  $v = $ba->verifikasi;
  $p = $v?->pengaduan;
  $hari = $v?->tanggal_verifikasi?->isoFormat('dddd');
  $tglTerbilang = $v?->tanggal_verifikasi?->isoFormat('D');
  $bulan = $v?->tanggal_verifikasi?->isoFormat('MMMM');
  $tahun = $v?->tanggal_verifikasi?->isoFormat('Y');
  $jam = $v?->jam_verifikasi ?? '—';
  $jenisAduan = is_array($p?->jenis_aduan) ? implode(', ', array_map(fn($k) => \App\Models\Pengaduan::$jenisAduanList[$k] ?? $k, $p->jenis_aduan)) : '—';
@endphp

<p class="text-justify">
  Pada hari ini <strong>{{ $hari }}</strong> tanggal <strong>{{ $tglTerbilang }}</strong>
  bulan <strong>{{ $bulan }}</strong> tahun <strong>{{ $tahun }}</strong>
  jam <strong>{{ $jam }} {{ $profil->zona_waktu ?? 'WIB' }}</strong>,
  berdasarkan Surat Tugas Kepala {{ $profil->nama_instansi ?? 'Dinas Lingkungan Hidup dan Kebersihan' }}
  {{ $profil->nama_kabupaten ?? 'Kabupaten Tangerang' }}, yang bertanda tangan di bawah ini:
</p>

{{-- ══ SECTION A: TIM VERIFIKATOR ══ --}}
<p class="section-header">A. Identitas Pengawas Lingkungan Hidup</p>
@if($v?->timVerifikator->isNotEmpty())
<table style="width:100%;border-collapse:collapse;font-size:11pt">
  @foreach($v->timVerifikator as $i => $tv)
  <tr>
    <td style="width:30px;vertical-align:top">{{ $i + 1 }}.</td>
    <td>
      Nama: <strong>{{ $tv->nama }}</strong><br>
      @if($tv->nip)NIP: {{ $tv->nip }}<br>@endif
      @if($tv->pangkat)Pangkat/Gol: {{ $tv->pangkat }}<br>@endif
      @if($tv->jabatan)Jabatan: {{ $tv->jabatan }}@endif
    </td>
  </tr>
  @endforeach
</table>
@endif

<p class="text-justify mt-3">
  Telah melakukan verifikasi lapangan terkait pengaduan dugaan pencemaran dan/atau perusakan lingkungan hidup
  pada hari <strong>{{ $hari }}</strong>, tanggal <strong>{{ $v?->tanggal_verifikasi?->isoFormat('D MMMM Y') }}</strong>
  Pukul <strong>{{ $jam }} {{ $profil->zona_waktu ?? 'WIB' }}</strong>
  dengan dugaan <strong>{{ $jenisAduan }}</strong>
  terhadap <strong>{{ $p?->terlapor?->nama ?? '—' }}</strong>.
</p>

{{-- ══ SECTION B: IDENTITAS PENANGGUNG JAWAB ══ --}}
<p class="section-header">B. Identitas Penanggung Jawab Kegiatan/Usaha</p>
@php $pj = $v?->penanggungJawab; @endphp
<table style="width:100%;border-collapse:collapse;font-size:11pt">
  <tr><td style="width:30px">1.</td><td style="width:220px">Nama Kegiatan/Badan Usaha</td><td>: {{ $pj?->nama_perusahaan ?? $p?->terlapor?->nama ?? '—' }}</td></tr>
  <tr><td>2.</td><td>Jenis Kegiatan Utama/KBLI</td><td>: {{ $pj?->kbli ?? '—' }}</td></tr>
  <tr><td>3.</td><td>Deskripsi Kegiatan</td><td>: {{ $pj?->deskripsi_kegiatan ?? $pj?->bidang_usaha ?? '—' }}</td></tr>
  <tr><td>4.</td><td>Alamat</td><td>: {{ $pj?->alamat_perusahaan ?? $p?->terlapor?->alamat ?? '—' }}</td></tr>
  <tr><td>5.</td><td>NIB</td><td>: {{ $pj?->nib ?? $p?->terlapor?->nib ?? '—' }}</td></tr>
  <tr><td>6.</td><td>Status Permodalan</td><td>: {{ $pj?->status_permodalan ?? '—' }}</td></tr>
  <tr><td>7.</td><td>No. Telepon</td><td>: {{ $pj?->no_telp ?? $p?->terlapor?->no_telp ?? '—' }}</td></tr>
  <tr><td>8.</td><td>Penanggung Jawab</td><td>: {{ $pj?->nama_pj ?? $v?->nama_penanggung_jawab ?? '—' }}</td></tr>
  <tr><td>9.</td><td>Jabatan</td><td>: {{ $pj?->jabatan_pj ?? $v?->jabatan_pj ?? '—' }}</td></tr>
  <tr><td>10.</td><td>Titik Koordinat</td><td>: {{ ($pj?->koordinat_lat ?? $v?->koordinat_lat) ? ($pj?->koordinat_lat ?? $v?->koordinat_lat) . ', ' . ($pj?->koordinat_lng ?? $v?->koordinat_lng) : '—' }}</td></tr>
</table>

{{-- ══ SECTION C: INFORMASI ADMINISTRASI ══ --}}
<p class="section-header">C. Informasi Administrasi Kegiatan</p>
<div class="text-box text-justify">{{ $v?->informasi_administrasi ?? '-' }}</div>

{{-- ══ SECTION D: FAKTA TEMUAN ══ --}}
<p class="section-header">D. Fakta Temuan Lapangan</p>
<div class="text-box text-justify">{{ $v?->fakta_temuan ?? '-' }}</div>

{{-- ══ SECTION E: SARAN TINDAK LANJUT ══ --}}
<p class="section-header">E. Saran Tindak Lanjut</p>
<div class="text-box text-justify">{{ $v?->saran_tindak_lanjut ?? '-' }}</div>

{{-- ══ KLAUSULA PENUTUP ══ --}}
<p class="text-justify mt-3">
  Pelaksanaan dan temuan fakta-fakta verifikasi lapangan di atas diketahui dan dibenarkan
  oleh <strong>{{ $p?->terlapor?->nama ?? '—' }}</strong> dan akan ditindaklanjuti
  selambat-lambatnya 14 (empat belas) hari kerja sejak tanggal Berita Acara ini dibuat.
</p>

<p class="text-justify">
  Demikian Berita Acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
</p>

<p class="mt-3">
  {{ $profil->nama_kabupaten ?? 'Kabupaten Tangerang' }}, {{ $ba->tanggal_terbit->isoFormat('D MMMM Y') }}
</p>

{{-- ══ TANDA TANGAN ══ --}}
<table class="sig-table">
  <tr>
    <td><strong>Penanggung Jawab Kegiatan</strong></td>
    <td><strong>Tim Verifikator</strong></td>
    <td><strong>Saksi-Saksi</strong></td>
  </tr>
  <tr>
    <td>
      <div class="sig-space"></div>
      <strong>{{ $pj?->nama_pj ?? $v?->nama_penanggung_jawab ?? '........................' }}</strong><br>
      <span style="font-size:9pt">{{ $pj?->jabatan_pj ?? $v?->jabatan_pj ?? '' }}</span>
    </td>
    <td>
      @foreach(($v?->timVerifikator ?? collect())->take(2) as $i => $tv)
        <div class="sig-space" style="height:{{ $i > 0 ? '40px' : '70px' }}"></div>
        {{ $i + 1 }}. <strong>{{ $tv->nama }}</strong><br>
        @if($tv->nip)<span style="font-size:9pt">NIP. {{ $tv->nip }}</span><br>@endif
      @endforeach
    </td>
    <td>
      @foreach(($v?->saksi ?? collect())->take(2) as $i => $s)
        <div class="sig-space" style="height:{{ $i > 0 ? '40px' : '70px' }}"></div>
        {{ $i + 1 }}. <strong>{{ $s->nama }}</strong><br>
        @if($s->jabatan)<span style="font-size:9pt">{{ $s->jabatan }}</span><br>@endif
      @endforeach
      @if(($v?->saksi ?? collect())->isEmpty())
        <div class="sig-space"></div>
        1. ........................<br>
        <div style="height:40px"></div>
        2. ........................
      @endif
    </td>
  </tr>
</table>

{{-- ══ QR CODE ══ --}}
@if(!empty($qrCode))
<div class="qr-section">
  <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
  <div class="qr-label">Scan untuk verifikasi keaslian</div>
</div>
@endif

{{-- ══ HALAMAN DOKUMENTASI ══ --}}
@if($v?->dokumentasiFoto->isNotEmpty())
<div class="page-break"></div>

<div class="ba-title">
  <h3>DOKUMENTASI FOTO VERIFIKASI LAPANGAN</h3>
  <p>{{ $ba->nomor_ba }}</p>
</div>

<table class="foto-grid">
  @foreach($v->dokumentasiFoto->chunk(3) as $row)
  <tr>
    @foreach($row as $foto)
    <td>
      @if(file_exists(storage_path('app/public/' . $foto->path_file)))
        <img src="{{ storage_path('app/public/' . $foto->path_file) }}" alt="{{ $foto->keterangan }}">
      @else
        <div style="height:140px;border:1px dashed #ccc;display:flex;align-items:center;justify-content:center;color:#999;font-size:10pt">Foto tidak ditemukan</div>
      @endif
      <div class="foto-caption">{{ $foto->keterangan ?? 'Foto ' . $foto->urutan }}</div>
    </td>
    @endforeach
    @for($j = $row->count(); $j < 3; $j++)
    <td></td>
    @endfor
  </tr>
  @endforeach
</table>
@endif

</body>
</html>
