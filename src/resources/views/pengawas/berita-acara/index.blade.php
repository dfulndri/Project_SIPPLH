@extends('layouts.pengawas')
@section('title', 'Berita Acara')
@section('breadcrumb', 'Berita Acara')

@section('content')
    <div class="page-hd mb-3">
        <h1 class="page-ttl">Berita Acara Saya</h1>
        <p class="page-stl">Daftar berita acara dari verifikasi yang Anda lakukan</p>
    </div>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table sipplh-table">
                <thead>
                    <tr>
                        <th>Nomor BA</th>
                        <th>No. Pengaduan</th>
                        <th>Terlapor</th>
                        <th>Tanggal Terbit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($beritaAcaras as $ba)
                        <tr>
                            <td><code>{{ $ba->nomor_ba }}</code></td>
                            <td style="font-size:.82rem">{{ $ba->verifikasi?->pengaduan?->nomor_pengaduan }}</td>
                            <td style="font-size:.82rem">{{ $ba->verifikasi?->pengaduan?->terlapor?->nama ?? '—' }}</td>
                            <td style="font-size:.82rem">{{ $ba->tanggal_terbit->format('d M Y') }}</td>
                            <td><span
                                    class="badge bg-{{ $ba->status == 'final' ? 'success' : 'warning' }}">{{ ucfirst($ba->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('pengawas.berita-acara.show', $ba) }}"
                                        class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('pengawas.berita-acara.pdf', $ba) }}"
                                        class="btn btn-xs btn-outline-success"><i class="bi bi-download"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state"><i class="bi bi-file-earmark-text"></i>Belum ada berita acara</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $beritaAcaras->links() }}</div>
    </div>
@endsection
