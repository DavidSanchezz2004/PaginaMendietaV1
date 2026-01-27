@php
  // Sidebar Cliente — rutas reales
@endphp

<link rel="stylesheet" href="{{ asset('css/barra.css') }}">

<aside class="sidebar" role="navigation" aria-label="Menú principal">
  <div class="brand">
    <img src="{{ asset('images/logo.png') }}" alt="Mendieta" />
    <div class="brand-title">
      <b>Portal Mendieta</b>
      <span>Panel de Cliente</span>
    </div>
  </div>

  <div class="divider"></div>
  <div class="section">Menú</div>

  <div class="nav">

    {{-- Inicio --}}
    <a class="item {{ request()->routeIs('cliente.panel') ? 'active' : '' }}"
       href="{{ route('cliente.panel') }}">
      <span class="item-left">
        <span class="icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M3 10.5L12 3l9 7.5"></path>
            <path d="M5 10v11h14V10"></path>
            <path d="M9 21v-6h6v6"></path>
          </svg>
        </span>
        <span class="label">Inicio</span>
      </span>
    </a>

    {{-- Reportes --}}
    <div class="group" data-group="reportes">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
              <path d="M4 4h16v16H4z"></path>
              <path d="M7 8h10"></path>
              <path d="M7 12h10"></path>
              <path d="M7 16h6"></path>
            </svg>
          </span>
          <span class="label">Reportes</span>
        </span>
        <span class="chev" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M6 9l6 6 6-6"></path>
          </svg>
        </span>
      </a>

      <div class="submenu" aria-label="Submenú Reportes">
        <a class="subitem {{ request()->routeIs('cliente.reportes.index') ? 'active' : '' }}"
           href="{{ route('cliente.reportes.index') }}">
          <span class="subicon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
              <path d="M8 6h13"></path>
              <path d="M8 12h13"></path>
              <path d="M8 18h13"></path>
              <path d="M3 6h.01"></path>
              <path d="M3 12h.01"></path>
              <path d="M3 18h.01"></path>
            </svg>
          </span>
          Mis reportes
        </a>
      </div>
    </div>

    {{-- Novedades (Noticias + Tutoriales en 1) --}}
    <a class="item {{ request()->routeIs('cliente.novedades.*') ? 'active' : '' }}"
       href="{{ route('cliente.novedades.index') }}">
      <span class="item-left">
        <span class="icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M4 4h16v16H4z"></path>
            <path d="M7 7h10"></path>
            <path d="M7 11h10"></path>
            <path d="M7 15h6"></path>
          </svg>
        </span>
        <span class="label">Novedades</span>
      </span>
    </a>

    {{-- Asistente IA --}}
    <a class="item {{ request()->routeIs('cliente.assistant.*') ? 'active' : '' }}"
       href="{{ route('cliente.assistant.show') }}">
      <span class="item-left">
        <span class="icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
          </svg>
        </span>
        <span class="label">Asistente Mendieta IA</span>
      </span>
    </a>

    {{-- Escríbenos (abre modal en frontend) --}}
    <a class="item" href="#" onclick="window.dispatchEvent(new CustomEvent('open-contact-modal')); return false;">
      <span class="item-left">
        <span class="icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path>
            <path d="M7 8h10"></path>
            <path d="M7 12h7"></path>
          </svg>
        </span>
        <span class="label">Escríbenos</span>
      </span>
    </a>

    {{-- Perfil --}}
    <a class="item {{ request()->routeIs('cliente.perfil') ? 'active' : '' }}"
       href="{{ route('cliente.perfil') }}">
      <span class="item-left">
        <span class="icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M20 21a8 8 0 0 0-16 0"></path>
            <path d="M12 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"></path>
          </svg>
        </span>
        <span class="label">Perfil</span>
      </span>
    </a>

  </div>

  <div class="sidebar-bottom">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout" type="submit">
        <span class="icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M10 17l5-5-5-5"></path>
            <path d="M15 12H3"></path>
            <path d="M21 3v18"></path>
          </svg>
        </span>
        Cerrar sesión
      </button>
    </form>
  </div>
</aside>
