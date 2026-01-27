@php
  // nada de routeIs acá porque aún no hay rutas
@endphp

<link rel="stylesheet" href="{{ asset('css/barra.css') }}">


<aside class="sidebar" role="navigation" aria-label="Menú principal">
  <div class="brand">
    <img src="{{ asset('images/logo.png') }}" alt="Mendieta" />
    <div class="brand-title">
      <b>Portal Mendieta</b>
      <span>Panel interno</span>
    </div>
  </div>

  <div class="divider"></div>
  <div class="section">Menú</div>

  <div class="nav">
    <!-- Dashboard -->
<a class="item {{ request()->routeIs('equipo.dashboard') ? 'active' : '' }}"
   href="{{ route('equipo.dashboard') }}">
      <span class="item-left">
        <span class="icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M4 13h7V4H4v9z"></path>
            <path d="M13 20h7V11h-7v9z"></path>
            <path d="M13 4h7v5h-7V4z"></path>
            <path d="M4 18h7v2H4v-2z"></path>
          </svg>
        </span>
        <span class="label">Dashboard</span>
      </span>
    </a>

    <!-- Empresas -->
    <div class="group" data-group="empresas">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
              <path d="M3 21h18"></path>
              <path d="M5 21V5a2 2 0 0 1 2-2h4v18"></path>
              <path d="M11 21V7h4a2 2 0 0 1 2 2v12"></path>
              <path d="M7 9h2"></path>
              <path d="M7 13h2"></path>
              <path d="M13 11h2"></path>
              <path d="M13 15h2"></path>
            </svg>
          </span>
          <span class="label">Empresas</span>
        </span>
        <span class="chev" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M6 9l6 6 6-6"></path>
          </svg>
        </span>
      </a>

      <div class="submenu" aria-label="Submenú Empresas">
        <a class="subitem" href="{{ route('equipo.empresas.create') }}" data-view="empresas_registrar">
          <span class="subicon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
              <path d="M12 5v14"></path>
              <path d="M5 12h14"></path>
            </svg>
          </span>
          Registrar empresa
        </a>

        <a class="subitem" href="{{ route('equipo.empresas.index') }}" data-view="empresas_listar">
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
          Listar empresas
        </a>
      </div>
    </div>

    <!-- Usuarios -->
<div class="group" data-group="usuarios">
  <a class="item" href="#" data-type="group">
    <span class="item-left">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
      </span>
      <span class="label">Usuarios</span>
    </span>
    <span class="chev" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M6 9l6 6 6-6"></path>
      </svg>
    </span>
  </a>

  <div class="submenu" aria-label="Submenú Usuarios">

    <a class="subitem" href="{{ route('equipo.usuarios.create') }}" data-view="usuarios_crear">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 5v14"></path>
          <path d="M5 12h14"></path>
        </svg>
      </span>
      Crear usuario
    </a>

    <a class="subitem" href="{{ route('equipo.usuarios.index') }}" data-view="usuarios_listar">
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
      Listar usuarios
    </a>

  </div>
</div>


   <!-- Reportes y Documentos -->
<div class="group" data-group="links">
  <a class="item" href="#" data-type="group">
    <span class="item-left">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"></path>
          <path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"></path>
        </svg>
      </span>
      <span class="label">Reportes y Documentos</span>
    </span>
    <span class="chev" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M6 9l6 6 6-6"></path>
      </svg>
    </span>
  </a>

  <div class="submenu" aria-label="Submenú Reportes">
    <a class="subitem" href="{{ route('equipo.reportes.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M3 3h18v18H3z"></path>
          <path d="M7 7h10"></path>
          <path d="M7 11h10"></path>
          <path d="M7 15h6"></path>
        </svg>
      </span>
      Listar Reportes
    </a>


    <a class="subitem" href="{{ route('equipo.reportes.create') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 5v14"></path>
          <path d="M5 12h14"></path>
        </svg>
      </span>
      Nuevo Reporte
    </a>
  </div>
</div>


    

    <!-- Portal Sunat -->
    <!-- Portal Sunat -->
<div class="group" data-group="sunat">
  <a class="item" href="#" data-type="group">
    <span class="item-left">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z"></path>
        </svg>
      </span>
      <span class="label">Portal Sunat</span>
    </span>
    <span class="chev" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M6 9l6 6 6-6"></path>
      </svg>
    </span>
  </a>

  <div class="submenu" aria-label="Submenú Portal Sunat">

    {{-- 1) Empresas --}}
    <a class="subitem" href="{{ route('equipo.empresas.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M3 21h18"></path>
          <path d="M5 21V5a2 2 0 0 1 2-2h4v18"></path>
          <path d="M11 21V7h4a2 2 0 0 1 2 2v12"></path>
        </svg>
      </span>
      Empresas
    </a>

    {{-- 2) Operadores (App Users) --}}
    <a class="subitem" href="{{ route('equipo.operadores.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M20 21a8 8 0 0 0-16 0"></path>
          <path d="M12 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"></path>
        </svg>
      </span>
      Operadores
    </a>

    {{-- 3) Credenciales (por portal/empresa) --}}
    <a class="subitem" href="{{ route('equipo.credenciales.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <rect x="3" y="11" width="18" height="10" rx="2"></rect>
          <path d="M7 11V8a5 5 0 0 1 10 0v3"></path>
        </svg>
      </span>
      Credenciales
    </a>

    {{-- 4) Asignaciones (operador ↔ portal_account) --}}
    <a class="subitem" href="{{ route('equipo.asignaciones.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M16 3h5v5"></path>
          <path d="M21 3l-7 7"></path>
          <path d="M8 21H3v-5"></path>
          <path d="M3 21l7-7"></path>
        </svg>
      </span>
      Asignaciones
    </a>

    {{-- 5) Jobs / Resultados --}}
    <a class="subitem" href="{{ route('equipo.jobs.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M9 11l3 3L22 4"></path>
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
        </svg>
      </span>
      Jobs / Resultados
    </a>

  </div>
</div>

{{-- Contenido (Noticias + Tutoriales) --}}
<div class="group" data-group="contenido">
  <a class="item" href="#" data-type="group">
    <span class="item-left">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M4 4h16v16H4z"></path>
          <path d="M7 7h10"></path>
          <path d="M7 11h10"></path>
          <path d="M7 15h6"></path>
        </svg>
      </span>
      <span class="label">Contenido</span>
    </span>
    <span class="chev" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M6 9l6 6 6-6"></path>
      </svg>
    </span>
  </a>

  <div class="submenu" aria-label="Submenú Contenido">
    <a class="subitem {{ request()->routeIs('equipo.noticias.*') ? 'active' : '' }}"
       href="{{ route('equipo.noticias.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M4 4h16v16H4z"></path>
          <path d="M7 8h10"></path>
          <path d="M7 12h10"></path>
          <path d="M7 16h6"></path>
        </svg>
      </span>
      Noticias
    </a>

    <a class="subitem {{ request()->routeIs('equipo.tutorials.*') ? 'active' : '' }}"
       href="{{ route('equipo.tutorials.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M8 5h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8"></path>
          <path d="M8 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2"></path>
          <path d="M10 9l6 3-6 3V9z"></path>
        </svg>
      </span>
      Tutoriales
    </a>
  </div>
</div>


{{-- <div class="group" data-group="facturas">
  <a class="item" href="#" data-type="group">
    <span class="item-left">
      <span class="icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
          <path d="M14 2v6h6"></path>
        </svg>
      </span>
      <span class="label">Comprobantes de Pago</span>
    </span>
    <span class="chev" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M6 9l6 6 6-6"></path>
      </svg>
    </span>
  </a>

  <div class="submenu" aria-label="Submenú Facturas">


    <a class="subitem" href="{{ route('equipo.facturas.create') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M3 3h18v18H3z"></path>
          <path d="M7 7h10"></path>
          <path d="M7 11h10"></path>
          <path d="M7 15h6"></path>
        </svg>
      </span>
      Cliente - Factura
    </a>

    <a class="subitem" href="{{ route('equipo.facturas.create') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M3 3h18v18H3z"></path>
          <path d="M7 7h10"></path>
          <path d="M7 11h10"></path>
          <path d="M7 15h6"></path>
        </svg>
      </span>
      Facturas
    </a>

    <a class="subitem" href="{{ route('equipo.facturas.index') }}">
      <span class="subicon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M3 3h18v18H3z"></path>
          <path d="M7 7h10"></path>
          <path d="M7 11h10"></path>
          <path d="M7 15h6"></path>
        </svg>
      </span>
      Listar Facturas
    </a>

  </div>

  
  

</div> --}}

    <!-- Perfil -->
  <a class="item {{ request()->routeIs('equipo.perfil') ? 'active' : '' }}"
   href="{{ route('equipo.perfil') }}">
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
