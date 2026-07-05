<nav id="sidebar">
  <div class="sb-brand">
    <div class="sb-icon"><i class="bi bi-shield-fill-check"></i></div>
    <div>
      <div class="sb-name">SIPPLH</div>
      <div class="sb-sub">Panel Pengawas</div>
    </div>
  </div>
  <div class="sb-user">
    <div class="sb-av">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
    <div style="overflow:hidden">
      <div class="sb-uname text-truncate">{{ auth()->user()->name }}</div>
      <div class="sb-urole">Pengawas Lapangan</div>
    </div>
  </div>
  <div class="sb-nav">
    <div class="sb-sec">Menu</div>

    <a href="{{ route('pengawas.dashboard') }}"
       class="sb-link {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>

    <a href="{{ route('pengawas.tugas.index') }}"
       class="sb-link {{ request()->routeIs('pengawas.tugas.*') ? 'active' : '' }}">
      <i class="bi bi-envelope-open-fill"></i><span>Pengaduan Saya</span>
      @php $pending = \App\Models\Pengaduan::where('assigned_to', auth()->id())->whereIn('status',['didisposisikan','verifikasi_lapangan'])->count(); @endphp
      @if($pending > 0)<span class="sb-badge">{{ $pending }}</span>@endif
    </a>

    <a href="{{ route('pengawas.verifikasi.index') }}"
       class="sb-link {{ request()->routeIs('pengawas.verifikasi.*') ? 'active' : '' }}">
      <i class="bi bi-clipboard-check-fill"></i><span>Verifikasi Lapangan</span>
    </a>

    <a href="{{ route('pengawas.berita-acara.index') }}"
       class="sb-link {{ request()->routeIs('pengawas.berita-acara.*') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-text-fill"></i><span>Berita Acara</span>
    </a>

    <a href="{{ route('pengawas.tindak-lanjut.index') }}"
       class="sb-link {{ request()->routeIs('pengawas.tindak-lanjut.*') ? 'active' : '' }}">
      <i class="bi bi-arrow-repeat"></i><span>Tindak Lanjut</span>
    </a>

    <div class="sb-sec">Akun</div>
    <a href="{{ route('pengawas.profil.edit') }}"
       class="sb-link {{ request()->routeIs('pengawas.profil.*') ? 'active' : '' }}">
      <i class="bi bi-person-circle"></i><span>Profil</span>
    </a>
  </div>

  <div class="sb-foot">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sb-link w-100 border-0 bg-transparent text-start" style="cursor:pointer">
        <i class="bi bi-box-arrow-left"></i><span>Keluar</span>
      </button>
    </form>
    <div class="sb-ver">SIPPLH v2.0 &copy; {{ date('Y') }}</div>
  </div>
</nav>
<div id="sidebar-overlay" onclick="closeSidebar()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1049"></div>