<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', config('app.name', 'Portal Mendieta'))</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="{{ asset('css/barra.css') }}">
  @stack('styles')
</head>

  <body>
    <div class="overlay" onclick="closeSidebar()" aria-hidden="true"></div>

    <header class="topbar">
      <button class="menu-btn" type="button" onclick="openSidebar()" aria-label="Abrir menú">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M4 6h16"></path>
          <path d="M4 12h16"></path>
          <path d="M4 18h16"></path>
        </svg>
      </button>

      <div class="brand-title">
        <b>Portal Mendieta</b>
        <span>@yield('topbar_subtitle', 'Panel de Clientes')</span>
      </div>
    </header>

    <div class="app">
      {{-- ✅ Sidebar CLIENTE --}}
      @include('profile.partials.sidebar-cliente')

      <main class="content">
        <div class="content-shell" id="main-content">
          @yield('content')
        </div>
      </main>
    </div>

    @stack('scripts')
    <script src="{{ asset('js/barra.js') }}?v={{ filemtime(public_path('js/barra.js')) }}"></script>
  </body>
</html>
