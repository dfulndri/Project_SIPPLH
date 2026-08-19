<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Berita Acara {{ $ba->nomor_ba }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #000;
            margin: 0;
        }

        p {
            margin: 0 0 8px;
        }

        .tc {
            text-align: center;
        }

        .tr {
            text-align: right;
        }

        .tj {
            text-align: justify;
        }

        .b {
            font-weight: bold;
        }

        .u {
            text-decoration: underline;
        }

        .mt10 {
            margin-top: 10px;
        }

        .mt15 {
            margin-top: 15px;
        }

        /* ── KOP SURAT ── */
        .kop {
            width: 100%;
            border-collapse: collapse;
        }

        .kop td {
            vertical-align: middle;
            padding: 0;
        }

        .kop .logo-cell {
            width: 90px;
            text-align: center;
        }

        .kop .logo-cell img {
            width: 80px;
            height: auto;
        }

        .kop .txt-cell {
            text-align: center;
        }

        .kop .txt-cell .l1 {
            font-size: 13pt;
            margin: 0;
        }

        .kop .txt-cell .l2 {
            font-size: 17pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: .5px;
        }

        .kop .txt-cell .l3 {
            font-size: 8pt;
            margin: 3px 0 0;
            line-height: 1.3;
        }

        .kop-line {
            border-bottom: 3px solid #000;
            margin: 3px 0 2px;
        }

        .kop-line2 {
            border-bottom: 1px solid #000;
            margin: 0 0 14px;
        }

        /* ── JUDUL ── */
        .title {
            text-align: center;
            margin: 6px 0 14px;
        }

        .title h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.35;
        }

        /* ── SECTION ── */
        .sec {
            font-weight: bold;
            font-size: 11pt;
            margin: 12px 0 6px;
        }

        /* ── Identitas Pengawas (A) ── */
        .idn {
            width: 100%;
            border-collapse: collapse;
        }

        .idn td {
            vertical-align: top;
            padding: 0 0 1px;
            font-size: 11pt;
        }

        .idn .no {
            width: 26px;
        }

        .idn .key {
            width: 120px;
        }

        .idn .sep {
            width: 12px;
        }

        /* ── Identitas PJ (B) ── */
        .pj {
            width: 100%;
            border-collapse: collapse;
        }

        .pj td {
            vertical-align: top;
            padding: 1px 0;
            font-size: 11pt;
        }

        .pj .no {
            width: 26px;
        }

        .pj .key {
            width: 210px;
        }

        .pj .sep {
            width: 12px;
        }

        /* ── Uraian box (C/D/E) ── */
        .box {
            border: 1px solid #000;
            padding: 8px 10px;
            min-height: 55px;
            font-size: 11pt;
            text-align: justify;
        }

        .box p {
            margin: 0 0 4px;
        }

        .box ul,
        .box ol {
            margin: 4px 0;
            padding-left: 22px;
        }

        .box img {
            max-width: 100%;
            height: auto;
        }

        .box table {
            border-collapse: collapse;
            width: 100%;
            margin: 4px 0;
        }

        .box table td,
        .box table th {
            border: 1px solid #555;
            padding: 3px 6px;
        }

        /* ── Tanda tangan ── */
        .ttd-block {
            margin-top: 10px;
        }

        .ttd-block .hdr {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .ttd-item {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .ttd-item td {
            vertical-align: top;
            font-size: 11pt;
            padding: 0;
        }

        .ttd-item .no {
            width: 24px;
        }

        .ttd-item .key {
            width: 90px;
        }

        .ttd-item .sep {
            width: 10px;
        }

        .sign-gap {
            height: 55px;
        }

        /* ── Dokumentasi ── */
        .foto-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .foto-grid td {
            width: 50%;
            padding: 6px;
            text-align: center;
            vertical-align: top;
        }

        .foto-grid img {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #999;
        }

        .foto-cap {
            font-size: 9pt;
            margin-top: 4px;
            color: #333;
        }

        /* ── QR ── */
        .qr {
            margin-top: 16px;
        }

        .qr img {
            width: 90px;
            height: 90px;
        }

        .qr .lbl {
            font-size: 7.5pt;
            color: #555;
        }

        .pb {
            page-break-before: always;
        }
    </style>
</head>

<body>

    @php
        $v = $ba->verifikasi;
        $p = $v?->pengaduan;
        $pj = $v?->penanggungJawab;
        $tv = $v?->timVerifikator ?? collect();
        $sk = $v?->saksi ?? collect();
        $tgl = $v?->tanggal_verifikasi;
        $hari = $tgl?->locale('id')->isoFormat('dddd');
        $tglAngka = $tgl ? \App\Support\Terbilang::angka((int) $tgl->locale('id')->isoFormat('D')) : null;
        $bulan = $tgl?->locale('id')->isoFormat('MMMM');
        $tahun = $tgl?->isoFormat('Y');
        $jam = $v?->jam_verifikasi
            ? \Illuminate\Support\Str::of($v->jam_verifikasi)->substr(0, 5)->replace(':', '.')
            : '—';
        $zona = $profil->zona_waktu ?? 'WIB';
        $jenisAduan = is_array($p?->jenis_aduan)
            ? implode(
                ', ',
                array_map(
                    fn($k) => \App\Models\Pengaduan::$jenisAduanList[$k] ?? ucwords(str_replace('_', ' ', $k)),
                    $p->jenis_aduan,
                ),
            )
            : '—';
        $namaTerlapor = $pj?->nama_perusahaan ?? ($p?->terlapor?->nama ?? '—');
        $jenisTerlapor = $p?->terlapor?->jenis_terlapor ?? 'perorangan';
        $pjLabel = [
            'perorangan' => [
                'identitas' => null,
                'bidang' => 'Bidang Usaha',
                'deskripsi' => 'Deskripsi Kegiatan',
                'pj' => 'Penanggung Jawab',
            ],
            'lembaga' => [
                'identitas' => 'Nama Lembaga',
                'bidang' => 'Jenis/Kategori Lembaga',
                'deskripsi' => 'Deskripsi Kegiatan',
                'pj' => 'Penanggung Jawab',
            ],
            'badan_hukum' => [
                'identitas' => 'Nama Perusahaan',
                'bidang' => 'Bidang Usaha',
                'deskripsi' => 'Deskripsi Kegiatan',
                'pj' => 'Penanggung Jawab',
            ],
            'objek_lainnya' => [
                'identitas' => 'Nama/Identitas Objek',
                'bidang' => 'Jenis Objek',
                'deskripsi' => 'Deskripsi Objek',
                'pj' => 'Pemilik/Pengelola',
            ],
        ][$jenisTerlapor];
        $koord =
            $pj?->koordinat_lat ?? $v?->koordinat_lat
                ? ($pj?->koordinat_lat ?? $v?->koordinat_lat) . ',' . ($pj?->koordinat_lng ?? $v?->koordinat_lng)
                : '—';
    @endphp

    {{-- ══════ KOP SURAT ══════ --}}
    <table class="kop">
        <tr>
            <td class="logo-cell">
                @if ($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </td>
            <td class="txt-cell">
                <div class="l1">PEMERINTAH {{ strtoupper($profil->nama_kabupaten ?? 'KABUPATEN TANGERANG') }}</div>
                <div class="l2">{{ strtoupper($profil->nama_instansi ?? 'DINAS LINGKUNGAN HIDUP DAN KEBERSIHAN') }}
                </div>
                <div class="l3">
                    {{ $profil->alamat ?? 'Jl. Atik Soewardi Nomor 1, Gedung Lingkup PU LT Dasar-Puspem Tigaraksa, Tangerang, Banten, 15720' }}<br>
                    Telepon {{ $profil->telepon ?? '081188881398' }}, Laman
                    {{ $profil->website ?? 'dlhk.tangerangkab.go.id' }},<br>
                    Pos-el {{ $profil->email ?? 'dlhk.kabtangerang1@gmail.com' }}
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>
    <div class="kop-line2"></div>

    {{-- ══════ JUDUL ══════ --}}
    <div class="title">
        <h3>BERITA ACARA HASIL VERIFIKASI LAPANGAN<br>
            PENGADUAN DUGAAN PENCEMARAN DAN/ATAU PERUSAKAN LINGKUNGAN HIDUP</h3>
    </div>

    {{-- ══════ PEMBUKA ══════ --}}
    <p class="tj">
        Pada hari ini <span class="b">{{ $hari ?? '—' }}</span> tanggal <span
            class="b">{{ $tglAngka ?? '—' }}</span>
        bulan <span class="b">{{ $bulan ?? '—' }}</span> tahun <span class="b">{{ $tahun ?? '—' }}</span>
        jam <span class="b">{{ $jam }} {{ $zona }}</span><br>
        Kami yang bertanda tangan dibawah ini :
    </p>

    {{-- ══════ A. IDENTITAS PENGAWAS ══════ --}}
    <p class="sec">A. Identitas Pengawas Lingkungan Hidup</p>
    <table class="idn">
        @forelse($tv as $i => $t)
            <tr>
                <td class="no">{{ $i + 1 }}.</td>
                <td class="key">Nama</td>
                <td class="sep">:</td>
                <td class="b">{{ $t->nama }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="key">NIP</td>
                <td class="sep">:</td>
                <td>{{ $t->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="key">Pangkat/Gol</td>
                <td class="sep">:</td>
                <td>{{ $t->pangkat ?? '-' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="key">Jabatan</td>
                <td class="sep">:</td>
                <td>{{ $t->jabatan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4"><em>Belum ada data tim verifikator.</em></td>
            </tr>
        @endforelse
    </table>

    <p class="tj mt15">
        Telah melakukan verifikasi lapangan terkait pengaduan dugaan pencemaran dan/atau
        perusakan lingkungan hidup pada <span class="b">{{ $hari }},
            {{ $tgl?->locale('id')->isoFormat('D MMMM Y') }}</span>
        Pukul <span class="b">{{ $jam }}</span> dengan dugaan
        <span class="b">{{ $jenisAduan }}</span> terhadap :<br>
        <span class="b">{{ $namaTerlapor }}</span>
    </p>

    {{-- ══════ B. IDENTITAS PENANGGUNG JAWAB ══════ --}}
    <p class="sec">B. Identitas Penanggung Jawab Usaha dan/atau Kegiatan</p>
    @php
        $pjRows = collect();
        if ($pjLabel['identitas']) {
            $identitasValue =
                $jenisTerlapor === 'objek_lainnya' ? ($p?->terlapor?->nama ?: '-') : ($pj?->nama_perusahaan ?: '-');
            $pjRows->push([$pjLabel['identitas'], $identitasValue, true]);
        }
        if ($jenisTerlapor === 'badan_hukum') {
            $pjRows->push(['Jenis Kegiatan Utama (KBLI)', $pj?->kbli_display ?? '-']);
        }
        $pjRows->push([$pjLabel['bidang'], $pj?->bidang_usaha ?: '-']);
        $pjRows->push([$pjLabel['deskripsi'], $pj?->deskripsi_kegiatan ?: '-']);
        $pjRows->push(['Alamat Lokasi Kegiatan', $pj?->alamat_perusahaan ?? ($p?->terlapor?->alamat ?? '-')]);
        if ($jenisTerlapor === 'badan_hukum') {
            $pjRows->push(['NIB', $pj?->nib ?? ($p?->terlapor?->nib ?? '-')]);
            $pjRows->push(['Status Permodalan', $pj?->status_permodalan ?? '-']);
        }
        $pjRows->push(['No Tlp / Fax', $pj?->no_telp ?? ($p?->terlapor?->no_telp ?? '-')]);
        $pjRows->push(['Email', $pj?->email ?? '-']);
        $pjRows->push([$pjLabel['pj'], $pj?->nama_pj ?? ($v?->nama_penanggung_jawab ?? '-')]);
        $pjRows->push(['Jabatan', $pj?->jabatan_pj ?? ($v?->jabatan_pj ?? '-')]);
        $pjRows->push(['Titik Koordinat Lokasi Kegiatan', $koord]);
    @endphp
    <table class="pj">
        @foreach ($pjRows as $i => $row)
            <tr>
                <td class="no">{{ $i + 1 }}.</td>
                <td class="key">{{ $row[0] }}</td>
                <td class="sep">:</td>
                <td class="{{ $row[2] ?? false ? 'b' : '' }}">{{ $row[1] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- ══════ C. INFORMASI ADMINISTRASI ══════ --}}
    <p class="sec">C. Informasi Administrasi Kegiatan</p>
    <div class="box">{!! $v?->informasi_administrasi ?: '-' !!}</div>

    {{-- ══════ D. FAKTA TEMUAN ══════ --}}
    <p class="sec">D. Fakta Temuan Lapangan Terkait Laporan Pengaduan</p>
    <div class="box">{!! $v?->fakta_temuan ?: '-' !!}</div>

    {{-- ══════ E. SARAN TINDAK LANJUT ══════ --}}
    <p class="sec">E. Saran Tindak Lanjut</p>
    <div class="box">{!! $v?->saran_tindak_lanjut ?: '-' !!}</div>

    {{-- ══════ KLAUSUL PENUTUP ══════ --}}
    <p class="tj mt15">
        Pelaksanaan dan temuan fakta-fakta verifikasi pengaduan lingkungan hidup ini diketahui
        dan dibenarkan oleh pihak <span class="b">Terlapor</span> dan akan ditindaklanjuti
        selambat-lambatnya 14 (empat belas) hari setelah Berita Acara ini ditandatangani.
    </p>

    {{-- ══════ TANDA TANGAN (3 BLOK) ══════ --}}
    <div style="margin-top:12px;">
        {{-- Blok 1: Penanggung Jawab Kegiatan --}}
        <div class="ttd-block">
            <div class="hdr">Tim Penanggung Jawab Kegiatan</div>
            @php
                $pjList = collect();
                if ($pj?->nama_pj) {
                    $pjList->push(['nama' => $pj->nama_pj, 'jabatan' => $pj->jabatan_pj]);
                } elseif ($v?->nama_penanggung_jawab) {
                    $pjList->push(['nama' => $v->nama_penanggung_jawab, 'jabatan' => $v->jabatan_pj]);
                }
                if ($pjList->isEmpty()) {
                    $pjList->push(['nama' => '........................', 'jabatan' => '']);
                }
            @endphp
            @foreach ($pjList as $i => $x)
                <table class="ttd-item">
                    <tr>
                        <td class="no">{{ $i + 1 }}.</td>
                        <td class="key">Nama</td>
                        <td class="sep">:</td>
                        <td class="b">{{ $x['nama'] }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="key">Jabatan</td>
                        <td class="sep">:</td>
                        <td>{{ $x['jabatan'] ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="key">Tanda Tangan</td>
                        <td class="sep">:</td>
                        <td></td>
                    </tr>
                </table>
                <div class="sign-gap"></div>
            @endforeach
        </div>

        {{-- Blok 2: Tim Verifikator --}}
        <div class="ttd-block">
            <div class="hdr">Tim Verifikator</div>
            @forelse($tv as $i => $t)
                <table class="ttd-item">
                    <tr>
                        <td class="no">{{ $i + 1 }}.</td>
                        <td class="key">Nama</td>
                        <td class="sep">:</td>
                        <td class="b">{{ $t->nama }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="key">Jabatan</td>
                        <td class="sep">:</td>
                        <td>{{ $t->jabatan ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="key">Tanda Tangan</td>
                        <td class="sep">:</td>
                        <td></td>
                    </tr>
                </table>
                <div class="sign-gap"></div>
            @empty
                <em>-</em>
            @endforelse
        </div>
    </div>

    {{-- Blok 3: Saksi-Saksi --}}
    <div class="ttd-block" style="margin-top:8px;">
        <div class="hdr">Saksi - Saksi</div>
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                @php $skList = $sk->isNotEmpty() ? $sk : collect([(object)['nama'=>'........................','jabatan'=>''],(object)['nama'=>'........................','jabatan'=>'']]); @endphp
                @foreach ($skList as $i => $s)
                    <td style="width:50%; vertical-align:top; padding:0 10px 0 0;">
                        <table class="ttd-item">
                            <tr>
                                <td class="no">{{ $i + 1 }}.</td>
                                <td class="key">Nama</td>
                                <td class="sep">:</td>
                                <td class="b">{{ $s->nama }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="key">Jabatan</td>
                                <td class="sep">:</td>
                                <td>{{ $s->jabatan ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="key">Tanda Tangan</td>
                                <td class="sep">:</td>
                                <td></td>
                            </tr>
                        </table>
                        <div class="sign-gap"></div>
                    </td>
                    @if ($i % 2 == 1)
            </tr>
            <tr>
                @endif
                @endforeach
            </tr>
        </table>
    </div>

    @php
        $qrBlock =
            '
  <table style="width:100%; margin-top:10px;"><tr>
    <td style="text-align:left; vertical-align:bottom; font-size:9pt;">
      Nomor BA: <span class="b">' .
            $ba->nomor_ba .
            '</span><br>
      ' .
            ($profil->nama_kabupaten ?? 'Kabupaten Tangerang') .
            ', ' .
            $ba->tanggal_terbit->locale('id')->isoFormat('D MMMM Y') .
            '
    </td>
    <td style="text-align:right;">
      <div class="qr">
        <img src="data:image/svg+xml;base64,' .
            $qrCode .
            '" alt="QR">
        <div class="lbl">Scan untuk verifikasi keaslian dokumen</div>
      </div>
    </td>
  </tr></table>';
    @endphp

    {{-- Kalau tidak ada foto dokumentasi sama sekali, QR tetap tampil di sini sebagai jaga-jaga --}}
    @if (!empty($qrCode) && $v?->dokumentasiFoto->isEmpty())
        {!! $qrBlock !!}
    @endif

    {{-- ══════ HALAMAN DOKUMENTASI ══════ --}}
    @if ($v?->dokumentasiFoto->isNotEmpty())
        <div class="pb"></div>
        <div class="title">
            <h3>DOKUMENTASI KEGIATAN VERIFIKASI LAPANGAN<br>
                PENGADUAN DUGAAN PENCEMARAN DAN/ATAU PERUSAKAN LINGKUNGAN HIDUP</h3>
        </div>
        <table class="foto-grid">
            @foreach ($v->dokumentasiFoto->chunk(2) as $row)
                <tr>
                    @foreach ($row as $foto)
                        <td>
                            @php $fotoAbs = storage_path('app/public/' . $foto->path_file); @endphp
                            @if (file_exists($fotoAbs))
                                <img src="{{ \App\Support\ImageResizer::forPdf($fotoAbs) }}" alt="foto">
                            @else
                                <div style="height:180px;border:1px dashed #bbb;color:#999;padding-top:80px;">Foto tidak
                                    ditemukan</div>
                            @endif
                            <div class="foto-cap">{{ $foto->keterangan ?: 'Dokumentasi ' . $foto->urutan }}</div>
                        </td>
                    @endforeach
                    @if ($row->count() < 2)
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </table>

        {{-- QR Code diletakkan di bawah foto-foto dokumentasi --}}
        @if (!empty($qrCode))
            {!! $qrBlock !!}
        @endif
    @endif

</body>

</html>
