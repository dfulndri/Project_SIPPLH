@extends('layouts.pengawas')
@section('title', 'Verifikasi Saya')
@section('breadcrumb', 'Verifikasi Lapangan')

@section('content')
    <div class="page-hd mb-3">
        <h1 class="page-ttl">Verifikasi Lapangan Saya</h1>
        <p class="page-stl">Daftar verifikasi lapangan yang Anda lakukan</p>
    </div>

    <div class="card-panel">
        <div class="table-responsive">
            <table class="table sipplh-table">
                <thead>
                    <tr>
                        <th>No. Pengaduan</th>
                        <th>Terlapor</th>
                        <th>Tanggal Verifikasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($verifikasis as $v)
                        <tr>
                            <td><code>{{ $v->pengaduan?->nomor_pengaduan }}</code></td>
                            <td style="font-size:.82rem">{{ $v->pengaduan?->terlapor?->nama ?? '—' }}</td>
                            <td style="font-size:.82rem">{{ $v->tanggal_verifikasi->format('d M Y') }}</td>
                            <td><span
                                    class="badge bg-{{ $v->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($v->status) }}</span>
                            </td>
                            <td>
                                @if ($v->pengaduan?->status !== \App\Models\Pengaduan::STATUS_ARSIP)
                                    <a href="{{ route('pengawas.verifikasi.edit', $v) }}"
                                        class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @endif
                                <a href="{{ route('pengawas.tugas.show', $v->pengaduan_id) }}"
                                    class="btn btn-xs btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state"><i class="bi bi-clipboard-check"></i>Belum ada verifikasi</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $verifikasis->links() }}</div>
    </div>
@endsection
