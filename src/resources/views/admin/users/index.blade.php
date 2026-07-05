@extends('layouts.admin')
@section('title','Manajemen User')
@section('breadcrumb','Manajemen User')

@section('content')

<div class="page-hd d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1 class="page-ttl">Manajemen User</h1>
    <p class="page-stl">Kelola akun pengguna sistem SIPPLH.</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-maroon">
    <i class="bi bi-person-plus-fill me-1"></i> Tambah User
  </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-check-circle-fill flex-shrink-0"></i> {{ session('success') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" style="font-size:.85rem">
  <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i> {{ session('error') }}
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filter --}}
<div class="card-panel mb-3">
  <div class="cp-body py-3">
    <form method="GET">
      <div class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Cari</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control"
              placeholder="Nama atau email..." value="{{ request('search') }}">
          </div>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label mb-1" style="font-size:.78rem;font-weight:500">Role</label>
          <select name="role" class="form-select form-select-sm">
            <option value="">Semua Role</option>
            <option value="admin"    {{ request('role')=='admin'    ? 'selected':'' }}>Admin</option>
            <option value="pengawas" {{ request('role')=='pengawas' ? 'selected':'' }}>Pengawas</option>
            <option value="viewer"   {{ request('role')=='viewer'   ? 'selected':'' }}>Viewer</option>
          </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-maroon flex-fill">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tabel --}}
<div class="card-panel">
  <div class="cp-head">
    <div class="cp-title">Daftar Pengguna</div>
    <div class="cp-sub">Total: <strong>{{ $users->total() }}</strong> pengguna</div>
  </div>
  <div class="table-responsive">
    <table class="table sipplh-table">
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th>Nama</th>
          <th>Email</th>
          <th>NIP</th>
          <th>Jabatan</th>
          <th>Role</th>
          <th>Status</th>
          <th style="width:120px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td class="text-muted" style="font-size:.75rem">{{ $users->firstItem() + $loop->index }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div style="width:32px;height:32px;background:{{ $user->role==='admin' ? 'var(--maroon)' : 'var(--maroon-md)' }};color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:600;flex-shrink:0">
                {{ strtoupper(substr($user->name,0,1)) }}
              </div>
              <div>
                <div style="font-size:.85rem;font-weight:500">{{ $user->name }}</div>
                @if($user->id === auth()->id())
                <span style="font-size:.68rem;background:var(--mint);color:var(--maroon);padding:1px 5px;border-radius:3px;font-weight:500">Anda</span>
                @endif
              </div>
            </div>
          </td>
          <td style="font-size:.82rem">{{ $user->email }}</td>
          <td><code style="font-size:.78rem">{{ $user->nip ?: '—' }}</code></td>
          <td style="font-size:.82rem">{{ $user->jabatan ?: '—' }}</td>
          <td>
            @php $roleColor = match($user->role) { 'admin'=>'var(--maroon)', 'pengawas'=>'var(--maroon-md)', default=>'#64748b' }; @endphp
            <span class="badge" style="background:{{ $roleColor }};font-size:.72rem">
              {{ ucfirst($user->role) }}
            </span>
          </td>
          <td>
            @if($user->is_active)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
            @else
            <span class="badge bg-secondary"><i class="bi bi-pause-circle me-1"></i>Nonaktif</span>
            @endif
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.users.edit', $user) }}"
                class="btn btn-xs btn-outline-warning" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              {{-- Toggle Status --}}
              <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">
                @csrf @method('PATCH')
                <button type="submit"
                  class="btn btn-xs {{ $user->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                  title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                  {{ $user->id === auth()->id() ? 'disabled' : '' }}
                  onclick="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user ini?')">
                  <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                </button>
              </form>
              {{-- Hapus --}}
              @if($user->id !== auth()->id())
              <button type="button" class="btn btn-xs btn-outline-danger" title="Hapus"
                onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}', '{{ $user->name }}')">
                <i class="bi bi-trash"></i>
              </button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8">
            <div class="empty-state">
              <i class="bi bi-people"></i> Belum ada pengguna ditemukan.
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
  <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div style="font-size:.78rem;color:var(--muted)">
      Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}
    </div>
    {{ $users->links() }}
  </div>
  @endif
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title text-danger"><i class="bi bi-person-x-fill me-1"></i>Hapus User</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="font-size:.85rem">
        Yakin hapus akun <strong id="deleteNama"></strong>?
        <div class="text-muted mt-1" style="font-size:.78rem">Semua data terkait user ini akan terlepas.</div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="deleteForm" method="POST">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger">
            <i class="bi bi-trash me-1"></i> Ya, Hapus
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDelete(action, nama) {
  document.getElementById('deleteForm').action = action;
  document.getElementById('deleteNama').textContent = nama;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush