@php
  // $active: productos | paquetes | categorias
  $active = $active ?? 'productos';
@endphp

<div class="fact-tabsbar">
  <div class="fact-tabs">
    <a class="fact-tab {{ $active==='productos' ? 'active' : '' }}"
       href="{{ route('cliente.fact.productos.index') }}">Productos</a>

    <a class="fact-tab {{ $active==='paquetes' ? 'active' : '' }}"
       href="{{ route('cliente.fact.paquetes.index') }}">Paquetes</a>

    <a class="fact-tab {{ $active==='categorias' ? 'active' : '' }}"
       href="{{ route('cliente.fact.categorias.index') }}">Categorías</a>
  </div>

  <div class="fact-tabs-actions">
    {{-- Actions cambian según pantalla --}}
    @yield('fact-actions')
  </div>
</div>