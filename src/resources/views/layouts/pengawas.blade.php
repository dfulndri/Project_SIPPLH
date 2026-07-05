<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — SIPPLH Pengawas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('css/sipplh.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body>

  @include('layouts.partials._sidebar_pengawas')

  <div id="main-wrap">
    {{-- Topbar --}}
    <header id="topbar">
      <div class="tb-left">
        <button class="btn-sb-tog" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
        <div class="tb-crumb">
          <span>SIPPLH</span>
          <span class="mx-1 text-muted">/</span>
          <span class="cur">@yield('breadcrumb','Dashboard')</span>
        </div>
      </div>
      <div class="tb-right">
        <div class="dropdown">
          <button class="tb-user" data-bs-toggle="dropdown">
            <div class="tb-av">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
            <span class="d-none d-md-block ms-1">{{ auth()->user()->name }}</span>
            <i class="bi bi-chevron-down ms-1" style="font-size:.65rem;color:var(--muted)"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px">
            <li>
              <div class="px-3 py-2">
                <div style="font-size:.82rem;font-weight:600">{{ auth()->user()->name }}</div>
                <div style="font-size:.72rem;color:var(--muted)">{{ auth()->user()->jabatan ?? 'Pengawas' }}</div>
              </div>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item" href="{{ route('pengawas.profil.edit') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
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

    <main class="page-content">
      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <script>
    function toggleSidebar(){
      const sb=document.getElementById('sidebar'),ov=document.getElementById('sidebar-overlay');
      sb.classList.toggle('show');
      ov.style.display=sb.classList.contains('show')?'block':'none';
    }
    function closeSidebar(){
      document.getElementById('sidebar').classList.remove('show');
      document.getElementById('sidebar-overlay').style.display='none';
    }
  </script>
  @stack('scripts')
</body>
</html>