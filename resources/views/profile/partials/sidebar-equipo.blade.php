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

    {{-- Dashboard --}}
    <a class="item {{ request()->routeIs('equipo.dashboard') ? 'active' : '' }}"
       href="{{ route('equipo.dashboard') }}">
      <span class="item-left">
        <span class="icon"><i data-lucide="layout-dashboard"></i></span>
        <span class="label">Dashboard</span>
      </span>
    </a>

    {{-- Empresas --}}
    <div class="group" data-group="empresas">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon"><i data-lucide="building-2"></i></span>
          <span class="label">Empresas</span>
        </span>
        <span class="chev"><i data-lucide="chevron-down"></i></span>
      </a>

      <div class="submenu">
        <a class="subitem" href="{{ route('equipo.empresas.create') }}">
          <span class="subicon"><i data-lucide="plus"></i></span>
          Registrar empresa
        </a>

        <a class="subitem" href="{{ route('equipo.empresas.index') }}">
          <span class="subicon"><i data-lucide="list"></i></span>
          Listar empresas
        </a>
      </div>
    </div>

    {{-- Usuarios --}}
    <div class="group" data-group="usuarios">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon"><i data-lucide="users"></i></span>
          <span class="label">Usuarios</span>
        </span>
        <span class="chev"><i data-lucide="chevron-down"></i></span>
      </a>

      <div class="submenu">
        <a class="subitem" href="{{ route('equipo.usuarios.create') }}">
          <span class="subicon"><i data-lucide="user-plus"></i></span>
          Crear usuario
        </a>

        <a class="subitem" href="{{ route('equipo.usuarios.index') }}">
          <span class="subicon"><i data-lucide="list"></i></span>
          Listar usuarios
        </a>
      </div>
    </div>

    {{-- Reportes y Documentos --}}
    <div class="group" data-group="links">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon"><i data-lucide="folder"></i></span>
          <span class="label">Reportes y Documentos</span>
        </span>
        <span class="chev"><i data-lucide="chevron-down"></i></span>
      </a>

      <div class="submenu">
        <a class="subitem" href="{{ route('equipo.reportes.index') }}">
          <span class="subicon"><i data-lucide="file-text"></i></span>
          Listar Reportes
        </a>

        <a class="subitem" href="{{ route('equipo.reportes.create') }}">
          <span class="subicon"><i data-lucide="plus"></i></span>
          Nuevo Reporte
        </a>
      </div>
    </div>

    {{-- Portal Sunat --}}
    <div class="group" data-group="sunat">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon"><i data-lucide="shield"></i></span>
          <span class="label">Portal Sunat</span>
        </span>
        <span class="chev"><i data-lucide="chevron-down"></i></span>
      </a>

      <div class="submenu">
        <a class="subitem" href="{{ route('equipo.empresas.index') }}">
          <span class="subicon"><i data-lucide="building-2"></i></span>
          Empresas
        </a>

        <a class="subitem" href="{{ route('equipo.operadores.index') }}">
          <span class="subicon"><i data-lucide="user-check"></i></span>
          Operadores
        </a>

        <a class="subitem" href="{{ route('equipo.credenciales.index') }}">
          <span class="subicon"><i data-lucide="key"></i></span>
          Credenciales
        </a>

        <a class="subitem" href="{{ route('equipo.asignaciones.index') }}">
          <span class="subicon"><i data-lucide="git-branch"></i></span>
          Asignaciones
        </a>

        <a class="subitem" href="{{ route('equipo.jobs.index') }}">
          <span class="subicon"><i data-lucide="check-square"></i></span>
          Jobs / Resultados
        </a>
      </div>
    </div>

    {{-- Contenido --}}
    <div class="group" data-group="contenido">
      <a class="item" href="#" data-type="group">
        <span class="item-left">
          <span class="icon"><i data-lucide="book-open"></i></span>
          <span class="label">Contenido</span>
        </span>
        <span class="chev"><i data-lucide="chevron-down"></i></span>
      </a>

      <div class="submenu">
        <a class="subitem" href="{{ route('equipo.noticias.index') }}">
          <span class="subicon"><i data-lucide="newspaper"></i></span>
          Noticias
        </a>

        <a class="subitem" href="{{ route('equipo.tutorials.index') }}">
          <span class="subicon"><i data-lucide="play-circle"></i></span>
          Tutoriales
        </a>
      </div>
    </div>

    {{-- Perfil --}}
    <a class="item {{ request()->routeIs('equipo.perfil') ? 'active' : '' }}"
       href="{{ route('equipo.perfil') }}">
      <span class="item-left">
        <span class="icon"><i data-lucide="user"></i></span>
        <span class="label">Perfil</span>
      </span>
    </a>

  </div>

  <div class="sidebar-bottom">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="logout" type="submit">
        <span class="icon"><i data-lucide="log-out"></i></span>
        Cerrar sesión
      </button>
    </form>
  </div>
</aside>

{{-- ✅ Lucide (ponlo 1 vez en tu layout principal si puedes) --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>
