<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verifikasi Dokumen — SIPPLH</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root{--maroon:#6A0000;--mint:#C4F0C5;--mint-bg:#F1FFF1}
    body{background:var(--mint-bg);font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
    .verify-card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.1);max-width:620px;width:100%;overflow:hidden}
    .verify-header{background:var(--maroon);color:#fff;padding:2rem;text-align:center}
    .verify-header h1{font-size:1.1rem;font-weight:700;margin:0}
    .verify-body{padding:2rem}
    .status-icon{width:64px;height:64px;border-radius:50%;background:rgba(196,240,197,.3);border:2px solid var(--mint);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;color:var(--maroon)}
    .field{margin-bottom:.75rem}
    .field-label{font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;font-weight:600}
    .field-val{font-size:.9rem;color:#111;margin-top:2px}
    .divider{border-top:1px solid #f0f0f0;margin:1.25rem 0}
    .badge-valid{background:rgba(196,240,197,.5);color:#155724;border:1px solid #b2dfdb;border-radius:6px;padding:4px 12px;font-size:.82rem;font-weight:500}
  </style>
</head>
<body>

<div class="verify-card">
  <div class="verify-header">
    <div style="font-size:.8rem;opacity:.7;margin-bottom:4px">DINAS LINGKUNGAN HIDUP DAN KEBERSIHAN</div>
    <h1>KABUPATEN TANGERANG</h1>
    <div style="font-size:.75rem;opacity:.6;margin-top:4px">Sistem Verifikasi Dokumen Digital</div>
  </div>

  <div class="verify-body">
    <div class="status-icon"><i class="bi bi-shield-check-fill"></i></div>
    <h2 style="text-align:center;font-size:1rem;font-weight:700;margin-bottom:.25rem">Dokumen Terverifikasi</h2>
    <p style="text-align:center;font-size:.82rem;color:#6b7280;margin-bottom:1.5rem">
      Berita Acara ini adalah dokumen resmi yang diterbitkan oleh DLHK Kabupaten Tangerang.
    </p>

    <div class="d-flex justify-content-center mb-3">
      <span class="badge-valid"><i class="bi bi-patch-check-fill me-1"></i>Status: {{ ucfirst($ba->status) }}</span>
    </div>

    <div class="divider"></div>

    <div class="row g-3">
      <div class="col-6">
        <div class="field">
          <div class="field-label">Nomor BA</div>
          <div class="field-val"><strong>{{ $ba->nomor_ba }}</strong></div>
        </div>
      </div>
      <div class="col-6">
        <div class="field">
          <div class="field-label">Tanggal Terbit</div>
          <div class="field-val">{{ $ba->tanggal_terbit->isoFormat('D MMMM Y') }}</div>
        </div>
      </div>
      <div class="col-12">
        <div class="field">
          <div class="field-label">Nomor Pengaduan</div>
          <div class="field-val">{{ $ba->verifikasi?->pengaduan?->nomor_pengaduan ?? '—' }}</div>
        </div>
      </div>
      <div class="col-12">
        <div class="field">
          <div class="field-label">Terlapor / Perusahaan</div>
          <div class="field-val">{{ $ba->verifikasi?->pengaduan?->terlapor?->nama ?? '—' }}</div>
        </div>
      </div>
      <div class="col-12">
        <div class="field">
          <div class="field-label">Tim Verifikator</div>
          <div class="field-val">
            @forelse($ba->verifikasi?->timVerifikator ?? [] as $t)
            <div style="font-size:.85rem">{{ $t->nama }}
              @if($t->jabatan)<span class="text-muted"> — {{ $t->jabatan }}</span>@endif
            </div>
            @empty <span class="text-muted">—</span>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="divider"></div>

    <div style="background:#f9fafb;border-radius:8px;padding:.75rem 1rem;font-size:.75rem;color:#6b7280;text-align:center">
      <i class="bi bi-info-circle me-1"></i>
      Dokumen ini dapat diverifikasi keasliannya melalui sistem SIPPLH DLHK Kabupaten Tangerang.<br>
      Dicetak/diverifikasi pada: {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIB
    </div>
  </div>
</div>

</body>
</html>