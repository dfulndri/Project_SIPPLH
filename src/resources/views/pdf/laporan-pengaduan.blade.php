<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:Arial,sans-serif;font-size:9pt;color:#111}
  .page{padding:10mm 15mm}
  .kop{text-align:center;border-bottom:2px solid #6A0000;padding-bottom:8px;margin-bottom:12px}
  .kop h1{font-size:12pt;font-weight:bold}
  .kop h2{font-size:10pt}
  .doc-title{text-align:center;font-size:11pt;font-weight:bold;text-transform:uppercase;margin-bottom:8px}
  .meta{font-size:8.5pt;color:#555;margin-bottom:12px;text-align:center}
  table{width:100%;border-collapse:collapse;font-size:8.5pt}
  th,td{border:1px solid #ccc;padding:4px 6px}
  th{background:#f0e8e8;font-weight:bold;text-align:center}
  tr:nth-child(even){background:#fafafa}
  .badge{padding:2px 6px;border-radius:3px;font-size:7.5pt;font-weight:bold}
  .b-masuk{background:#e2e8f0;color:#475569}
  .b-diproses{background:#fef3c7;color:#92400e}
  .b-verifikasi{background:#dbeafe;color:#1e40af}
  .b-selesai{background:#d1fae5;color:#065f46}
  .b-ditolak{background:#fee2e2;color:#991b1b}
  .footer{margin-top:12px;font-size:7.5pt;color:#888;text-align:center;border-top:1px solid #ddd;padding-top:6px}
  .summary{display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap}
  .sum-box{border:1px solid #ddd;border-radius:4px;padding:6px 10px;text-align:center;flex:1;min-width:80px}
  .sum-num{font-size:14pt;font-weight:bold;color:#6A0000}
  .sum-lbl{font-size:7pt;color:#888;text-transform:uppercase}
</style>
</head>
<body>
<div class="page">
  <div class="kop">
    <h2>PEMERINTAH KABUPATEN TANGERANG</h2>
    <h1>DINAS LINGKUNGAN HIDUP DAN KEBERSIHAN</h1>
  </div>
  <div class="doc-title">LAPORAN REKAPITULASI PENGADUAN LINGKUNGAN HIDUP</div>
  <div class="meta">
    Dicetak: {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIB —
    Total: {{ $pengaduans->count() }} data
    @if($request->dari || $request->sampai)
    — Periode: {{ $request->dari ?? '—' }} s/d {{ $request->sampai ?? '—' }}
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:4%">No</th>
        <th style="width:14%">Nomor Pengaduan</th>
        <th style="width:10%">Tanggal</th>
        <th style="width:18%">Pelapor</th>
        <th style="width:18%">Terlapor</th>
        <th style="width:12%">Kategori</th>
        <th style="width:12%">Kecamatan</th>
        <th style="width:12%">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($pengaduans as $i => $p)
      <tr>
        <td style="text-align:center">{{ $i+1 }}</td>
        <td>{{ $p->nomor_pengaduan }}</td>
        <td style="text-align:center">{{ $p->tanggal_pengaduan->format('d/m/Y') }}</td>
        <td>{{ $p->pelapor?->anonim ? 'Anonim' : $p->pelapor?->nama_pelapor }}</td>
        <td>{{ $p->terlapor?->nama ?? '—' }}</td>
        <td>{{ ucwords(str_replace('_',' ',$p->kategori)) }}</td>
        <td>{{ $p->kecamatan?->nama_kecamatan ?? '—' }}</td>
        <td style="text-align:center">
          <span class="badge b-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center;color:#888;padding:12px">Tidak ada data</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    SIPPLH — Sistem Informasi Pengelolaan Pengaduan dan Verifikasi Lapangan Lingkungan Hidup<br>
    Dinas Lingkungan Hidup dan Kebersihan Kabupaten Tangerang
  </div>
</div>
</body>
</html>