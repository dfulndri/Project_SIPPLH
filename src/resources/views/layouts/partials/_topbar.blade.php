<header id="topbar">
  <div class="tb-left">
    {{-- Mobile toggle --}}
    <button class="btn-sb-tog" onclick="toggleSidebar()">
      <i class="bi bi-list"></i>
    </button>
    {{-- Breadcrumb --}}
    <div class="tb-crumb">
      <span>SIPPLH</span>
      <span class="mx-1 text-muted">/</span>
      <span class="cur">@yield('breadcrumb', 'Dashboard')</span>
    </div>
  </div>

  <div class="tb-right">

    {{-- Notifications --}}
    <div class="dropdown">
      <button class="tb-ic" data-bs-toggle="dropdown">
        <i class="bi bi-bell-fill"></i>
        <span class="notif-dot"></span>
      </button>
      <div class="dropdown-menu dropdown-menu-end notif-drop">
        <div class="dropdown-header">Notifikasi</div>
        @php
          $notifs = auth()->user()->notifikasi()->belumDibaca()->latest()->take(5)->get();
        @endphp
        @forelse($notifs as $n)
        <a href="#" class="notif-item text-decoration-none">
          <i class="bi bi-circle-fill text-{{ $n->tipe }}" style="font-size:.5rem;margin-top:5px;flex-shrink:0"></i>
          <div>
            <div class="notif-t">{{ $n->judul }}</div>
            <div class="notif-ts">{{ $n->created_at->diffForHumans() }}</div>
          </div>
        </a>
        @empty
        <div class="px-4 py-3 text-center" style="font-size:.8rem;color:var(--muted)">
          <i class="bi bi-bell-slash d-block mb-1"></i> Tidak ada notifikasi baru
        </div>
        @endforelse
        <div class="py-2 text-center border-top">
          <a href="#" style="font-size:.78rem;color:var(--maroon)">Lihat semua notifikasi</a>
        </div>
      </div>
    </div>

    {{-- Profile --}}
    <div class="dropdown">
      <button class="tb-user" data-bs-toggle="dropdown">
        <div class="tb-av">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <span class="d-none d-md-block ms-1">{{ auth()->user()->name }}</span>
        <i class="bi bi-chevron-down ms-1" style="font-size:.65rem;color:var(--muted)"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px">
        <li>
          <div class="px-3 py-2">
            <div style="font-size:.82rem;font-weight:600">{{ auth()->user()->name }}</div>
            <div style="font-size:.72rem;color:var(--muted)">{{ auth()->user()->email }}</div>
          </div>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="bi bi-box-arrow-right me-2"></i>Keluar
            </button>
          </form>
        </li>
      </ul>
    </div>

  </div>
</header>
