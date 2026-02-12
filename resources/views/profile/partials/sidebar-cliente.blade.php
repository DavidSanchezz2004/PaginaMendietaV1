@php
  // Sidebar Cliente — versión ligera (módulos)
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
        <span class="icon"><i data-lucide="home"></i></span>
        <span class="label">Inicio</span>
      </span>
    </a>

     <div class="divider"></div>
  <div class="section">Facturación</div>
  {{-- ✅ FACTURADOR (grupo desplegable) --}}
<div class="group" data-group="facturador">
  <a class="item {{ request()->routeIs('cliente.facturador.*') ? 'active' : '' }}"
     href="#"
     data-type="group">
    <span class="item-left">
      <span class="icon"><i data-lucide="file-text"></i></span>
      <span class="label">Facturador</span>
    </span>

    <span class="chev" aria-hidden="true">
      <i data-lucide="chevron-down"></i>
    </span>
  </a>

  <div class="submenu" aria-label="Submenú Facturador">

   <a class="subitem {{ request()->routeIs('cliente.facturador.ventas.*') ? 'active' : '' }}"
   href="{{ route('cliente.facturador.ventas.index') }}">
  <span class="subicon"><i data-lucide="shopping-cart"></i></span>
  Ventas
</a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.compras.*') ? 'active' : '' }}"
       href="">
      <span class="subicon"><i data-lucide="shopping-bag"></i></span>
      Compras
    </a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.productos.*') ? 'active' : '' }}"
       href="{{ route('cliente.facturador.productos.index') }}">
      <span class="subicon"><i data-lucide="package"></i></span>
      Productos
    </a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.clientes.*') ? 'active' : '' }}"
       href="{{ route('cliente.facturador.clientes.index') }}">
      <span class="subicon"><i data-lucide="users"></i></span>
      Clientes
    </a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.reportes.*') ? 'active' : '' }}"
       href="{{ route('cliente.facturador.reportes.index') }}">
      <span class="subicon"><i data-lucide="bar-chart-3"></i></span>
      Reportes
    </a>

    <!-- <a class="subitem {{ request()->routeIs('cliente.facturador.usuarios.*') ? 'active' : '' }}"
       href="">
      <span class="subicon"><i data-lucide="user-cog"></i></span>
      Usuarios
    </a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.gastos.*') ? 'active' : '' }}"
       href="">
      <span class="subicon"><i data-lucide="receipt"></i></span>
      Gastos
    </a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.transacciones.*') ? 'active' : '' }}"
       href="">
      <span class="subicon"><i data-lucide="arrow-left-right"></i></span>
      Transacciones
    </a>

    <a class="subitem {{ request()->routeIs('cliente.facturador.config.*') ? 'active' : '' }}"
       href="">
      <span class="subicon"><i data-lucide="settings"></i></span>
      Configuraciones
    </a> -->

  </div>
</div>

        <div class="divider"></div>
  <div class="section">Menu</div>
    {{-- Reportes (módulo global) --}}
    <a class="item {{ request()->routeIs('cliente.reportes.*') ? 'active' : '' }}"
       href="{{ route('cliente.reportes.index') }}">
      <span class="item-left">
        <span class="icon"><i data-lucide="clipboard-list"></i></span>
        <span class="label">Reportes</span>
      </span>
    </a>

    {{-- Novedades --}}
    <a class="item {{ request()->routeIs('cliente.novedades.*') ? 'active' : '' }}"
       href="{{ route('cliente.novedades.index') }}">
      <span class="item-left">
        <span class="icon"><i data-lucide="newspaper"></i></span>
        <span class="label">Novedades</span>
      </span>
    </a>

    {{-- Asistente IA --}}
    <a class="item {{ request()->routeIs('cliente.assistant.*') ? 'active' : '' }}"
       href="{{ route('cliente.assistant.show') }}">
      <span class="item-left">
        <span class="icon"><i data-lucide="bot"></i></span>
        <span class="label">Asistente Mendieta IA</span>
      </span>
    </a>

    {{-- Perfil --}}
    <a class="item {{ request()->routeIs('cliente.perfil') ? 'active' : '' }}"
       href="{{ route('cliente.perfil') }}">
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
        <span class="icon" aria-hidden="true"><i data-lucide="log-out"></i></span>
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
