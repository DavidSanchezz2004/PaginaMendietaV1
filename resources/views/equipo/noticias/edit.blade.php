@extends('layouts.app-equipo')

@section('title', 'Editar Noticia')
@section('topbar_subtitle', 'Noticias')

@section('content')
<div class="page">

  <div class="top">
    <div>
      <h1>Editar noticia</h1>
      <div class="sub">Actualiza el contenido o el estado</div>
    </div>

    <a href="{{ route('equipo.noticias.index') }}">Volver</a>
  </div>

  {{-- Errores --}}
  @if ($errors->any())
    <div style="padding:12px; margin:12px 0; border:1px solid #f3b4b4; background:#fff5f5; border-radius:10px;">
      <strong>Revisa el formulario:</strong>
      <ul style="margin:8px 0 0 18px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $categorias = $categorias ?? ['tributario','contable','laboral','finanzas','aviso'];
    // map status DB <-> form ES
    $estadoForm = old('estado') ?? (($news->status ?? 'draft') === 'published' ? 'publicado' : 'borrador');
  @endphp

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Detalle de noticia</h3>
        <p class="panel-sub">
          ID: <span class="mono">{{ $news->id }}</span>
          &nbsp; • &nbsp;
          Creado: {{ optional($news->created_at)->format('Y-m-d H:i') ?? '—' }}
          &nbsp; • &nbsp;
          Últ. edición: {{ optional($news->updated_at)->format('Y-m-d H:i') ?? '—' }}
        </p>
      </div>
    </div>

    <div style="padding:16px;">

      {{-- ✅ FORM 1: Guardar cambios --}}
      <form method="POST" action="{{ route('equipo.noticias.update', $news) }}">
        @csrf
        @method('PUT')

        <div style="margin-bottom:12px;">
          <label><b>Título</b></label><br>
          <input
            name="titulo"
            value="{{ old('titulo', $news->title) }}"
            required
            maxlength="190"
            style="width:100%;"
            placeholder="Ej: SUNAT: cambios en detracciones 2026"
          >
          @error('titulo') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
          <div style="min-width:240px;">
            <label><b>Categoría</b></label><br>
            <select name="categoria" required>
              @foreach($categorias as $c)
                <option value="{{ $c }}" @selected(old('categoria', $news->category) === $c)>
                  {{ ucfirst($c) }}
                </option>
              @endforeach
            </select>
            @error('categoria') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
          </div>

          <div style="min-width:240px;">
            <label><b>Estado</b></label><br>
            <select name="estado" required>
              <option value="borrador" @selected($estadoForm === 'borrador')>borrador</option>
              <option value="publicado" @selected($estadoForm === 'publicado')>publicado</option>
            </select>
            @error('estado') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
          </div>
        </div>

        <div style="margin-bottom:12px;">
          <label><b>Contenido</b></label><br>
          <textarea
            name="body"
            rows="10"
            required
            style="width:100%;"
            placeholder="Redacta la noticia aquí..."
          >{{ old('body', $news->body) }}</textarea>
          @error('body') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <div style="margin-bottom:12px;">
          <label><b>Extracto</b> <span style="font-size:12px; color:#666;">(opcional)</span></label><br>
          <textarea
            name="excerpt"
            rows="3"
            maxlength="300"
            style="width:100%;"
            placeholder="Resumen corto (máx 300)"
          >{{ old('excerpt', $news->excerpt) }}</textarea>
          @error('excerpt') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <div style="margin-bottom:12px;">
          <label><b>Cover URL</b> <span style="font-size:12px; color:#666;">(opcional)</span></label><br>
          <input
            name="cover_image_url"
            value="{{ old('cover_image_url', $news->cover_image_url) }}"
            maxlength="2000"
            style="width:100%;"
            placeholder="https://..."
          >
          @error('cover_image_url') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <hr style="margin:16px 0;">

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
          <button type="submit">Guardar cambios</button>
          <a href="{{ route('equipo.noticias.index') }}">Cancelar</a>
        </div>

        <p style="margin-top:10px; font-size:12px; color:#666;">
          Nota: si pones <b>publicado</b>, el cliente la verá en su portal.
          @if($news->published_at)
            &nbsp; • &nbsp; Publicado: {{ $news->published_at->format('Y-m-d H:i') }}
          @endif
          &nbsp; • &nbsp; Slug: <span class="mono">{{ $news->slug }}</span>
        </p>
      </form>

      {{-- ✅ FORM 2: Eliminar (separado) --}}
      <form method="POST" action="{{ route('equipo.noticias.destroy', $news) }}" style="margin-top:12px;">
        @csrf
        @method('DELETE')

        <button
          type="submit"
          onclick="return confirm('¿Eliminar esta noticia?')"
          style="width:100%;"
        >
          Eliminar noticia
        </button>
      </form>

    </div>
  </section>

</div>
@endsection
