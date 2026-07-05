<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — SIPPLH</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  {{-- Custom SIPPLH CSS --}}
  <link href="{{ asset('css/sipplh.css') }}" rel="stylesheet">

  @stack('styles')
</head>
<body>

  {{-- Sidebar --}}
  @include('layouts.partials._sidebar')

  {{-- Main Wrapper --}}
  <div id="main-wrap">

    {{-- Topbar --}}
    @include('layouts.partials._topbar')

    {{-- Page Content --}}
    <main class="page-content">
      @yield('content')
    </main>

  </div>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  {{-- Chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

  <script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
      const sb  = document.getElementById('sidebar');
      const ov  = document.getElementById('sidebar-overlay');
      const mw  = document.getElementById('main-wrap');
      sb.classList.toggle('show');
      ov.style.display = sb.classList.contains('show') ? 'block' : 'none';
    }

    function closeSidebar() {
      const sb = document.getElementById('sidebar');
      const ov = document.getElementById('sidebar-overlay');
      sb.classList.remove('show');
      ov.style.display = 'none';
    }

    // CSRF setup for AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  </script>

  @stack('scripts')
</body>
</html>
