@extends('layouts.app-cliente')

@section('title', 'Novedades')
@section('topbar_subtitle', 'Noticias y Tutoriales')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cliente_novedades.css') }}">
@endpush

@section('content')
<div class="page page-novedades">

  <div class="page-head">
    <h1 class="h1">Novedades</h1>
  </div>

  @if(session('ok'))
    <div class="alert alert-success">
      {{ session('ok') }}
    </div>
  @endif

  <div class="toolbar">
    <form method="GET" action="{{ route('cliente.novedades.index') }}" class="search-form">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Buscar..." class="input">
      <button class="btn primary">Buscar</button>
      @if(!empty($q))
        <a href="{{ route('cliente.novedades.index') }}" class="btn ghost">Limpiar</a>
      @endif
    </form>

    <button class="btn secondary" onclick="document.getElementById('modal-contacto').showModal()">
      Escríbenos ahora
    </button>
  </div>

  <hr class="divider">

  <h2 class="h2">Noticias</h2>
  <div class="grid cards-3">
    @forelse($news as $n)
      <article class="card">
        <div class="card-meta">
          {{ strtoupper($n->category) }} · {{ optional($n->published_at)->format('Y-m-d') }}
        </div>
        <h3 class="card-title">{{ $n->title }}</h3>
        <p class="card-text">{{ $n->excerpt }}</p>
        <a class="link" href="{{ route('cliente.news.show', $n->slug) }}">Leer artículo</a>
      </article>
    @empty
      <p class="muted">No hay noticias publicadas.</p>
    @endforelse
  </div>

  <hr class="divider">

  <h2 class="h2">Tutoriales y Guías</h2>
  <div class="grid cards-3">
    @forelse($tutorials as $t)
      <article class="card">
        <div class="card-meta">
          {{ strtoupper($t->category) }}
          @if($t->duration_label) · {{ $t->duration_label }} @endif
        </div>
        <h3 class="card-title">{{ $t->title }}</h3>
        <p class="card-text">{{ $t->excerpt }}</p>

        <div class="card-actions">
          <a class="link" href="{{ route('cliente.tutoriales.show', $t->slug) }}">Ver detalle</a>
          <a class="link strong" href="{{ route('cliente.tutoriales.ver', $t) }}">Ver tutorial</a>
        </div>
      </article>
    @empty
      <p class="muted">No hay tutoriales publicados.</p>
    @endforelse
  </div>

</div>

{{-- MODAL --}}
<dialog id="modal-contacto" class="modal">
  <form method="POST" action="{{ route('cliente.contacto.send') }}" class="modal-box">
    @csrf
    <h3 class="h3">Escríbenos ahora</h3>

    <div class="form-group">
      <label>Título</label>
      <input name="titulo" required maxlength="120" class="input">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Categoría</label>
        <select name="categoria" required class="input">
          <option value="tributario">Tributario</option>
          <option value="facturacion">Facturación</option>
          <option value="laboral">Laboral</option>
          <option value="otros">Otros</option>
        </select>
      </div>

      <div class="form-group">
        <label>Urgencia</label>
        <select name="urgencia" required class="input">
          <option value="baja">Baja</option>
          <option value="media">Media</option>
          <option value="alta">Alta</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>Mensaje</label>
      <textarea name="mensaje" rows="5" maxlength="4000" required class="input"></textarea>
    </div>

    <div class="modal-actions">
      <button type="button" class="btn ghost" onclick="document.getElementById('modal-contacto').close()">Cancelar</button>
      <button class="btn primary">Enviar</button>
    </div>
  </form>
</dialog>
@endsection
