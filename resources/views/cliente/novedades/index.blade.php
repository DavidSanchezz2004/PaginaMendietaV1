@extends('layouts.app-cliente')

@section('title', 'Novedades')
@section('topbar_subtitle', 'Noticias y Tutoriales')

@section('content')
  <h1>Novedades</h1>

  @if(session('ok'))
    <div style="padding:10px;border:1px solid #cce7d5;border-radius:10px;margin:10px 0;">
      {{ session('ok') }}
    </div>
  @endif

  <div style="display:flex; gap:10px; align-items:center; margin:12px 0;">
    <form method="GET" action="{{ route('cliente.novedades.index') }}" style="display:flex; gap:8px;">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Buscar..." />
      <button type="submit">Buscar</button>
      @if(!empty($q))
        <a href="{{ route('cliente.novedades.index') }}">Limpiar</a>
      @endif
    </form>

    <button type="button" onclick="document.getElementById('modal-contacto').showModal()">
      Escríbenos ahora
    </button>
  </div>

  <hr>

  <h2>Noticias</h2>
  <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px;">
    @forelse($news as $n)
      <div style="border:1px solid #eee; padding:12px; border-radius:12px;">
        <div style="font-size:12px; opacity:.7;">{{ strtoupper($n->category) }} · {{ optional($n->published_at)->format('Y-m-d') }}</div>
        <h3 style="margin:8px 0;">{{ $n->title }}</h3>
        <p style="opacity:.8;">{{ $n->excerpt }}</p>
        <a href="{{ route('cliente.news.show', $n->slug) }}">Leer artículo</a>
      </div>
    @empty
      <p>No hay noticias publicadas.</p>
    @endforelse
  </div>

  <hr style="margin:20px 0;">

  <h2>Tutoriales y Guías</h2>
  <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px;">
    @forelse($tutorials as $t)
      <div style="border:1px solid #eee; padding:12px; border-radius:12px;">
        <div style="font-size:12px; opacity:.7;">
          {{ strtoupper($t->category) }}
          @if($t->duration_label) · {{ $t->duration_label }} @endif
        </div>
        <h3 style="margin:8px 0;">{{ $t->title }}</h3>
        <p style="opacity:.8;">{{ $t->excerpt }}</p>

        <div style="display:flex; gap:10px;">
<a href="{{ route('cliente.tutoriales.show', $t->slug) }}">Ver detalle</a>

          <!-- ✅ NO link real -->
<!-- ✅ NO link real -->
<a href="{{ route('cliente.tutoriales.ver', $t) }}">Ver tutorial</a>
        </div>
      </div>
    @empty
      <p>No hay tutoriales publicados.</p>
    @endforelse
  </div>

  <!-- MODAL CONTACTO -->
  <dialog id="modal-contacto">
    <form method="POST" action="{{ route('cliente.contacto.send') }}" style="min-width:420px;">
      @csrf
      <h3>Escríbenos ahora</h3>

      <div style="margin:10px 0;">
        <label>Título</label><br>
        <input name="titulo" required maxlength="120" style="width:100%;" />
      </div>

      <div style="display:flex; gap:10px;">
        <div style="flex:1;">
          <label>Categoría</label><br>
          <select name="categoria" required style="width:100%;">
            <option value="tributario">Tributario</option>
            <option value="facturacion">Facturación</option>
            <option value="laboral">Laboral</option>
            <option value="otros">Otros</option>
          </select>
        </div>

        <div style="flex:1;">
          <label>Urgencia</label><br>
          <select name="urgencia" required style="width:100%;">
            <option value="baja">Baja</option>
            <option value="media">Media</option>
            <option value="alta">Alta</option>
          </select>
        </div>
      </div>

      <div style="margin:10px 0;">
        <label>Mensaje</label><br>
        <textarea name="mensaje" rows="5" required maxlength="4000" style="width:100%;"></textarea>
      </div>

      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" onclick="document.getElementById('modal-contacto').close()">Cancelar</button>
        <button type="submit">Enviar</button>
      </div>
    </form>
  </dialog>
@endsection
