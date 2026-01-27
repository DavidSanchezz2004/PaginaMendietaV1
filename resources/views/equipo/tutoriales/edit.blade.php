@extends('layouts.app-equipo')

@section('title', 'Editar Tutorial')
@section('topbar_subtitle', 'Contenido')

@section('content')
<h1>Editar Tutorial</h1>

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

<form method="POST" action="{{ route('equipo.tutorials.update', $tutorial) }}">
  @csrf
  @method('PUT')

  <div style="margin-bottom:12px;">
    <label>Título</label><br>
    <input name="title" value="{{ old('title', $tutorial->title) }}" required maxlength="190" style="width:100%;" />
  </div>

  <div style="display:flex; gap:10px; margin-bottom:12px;">
    <div style="flex:1;">
      <label>Categoría</label><br>
      <input name="category" value="{{ old('category', $tutorial->category) }}" required maxlength="40" style="width:100%;" />
    </div>

    <div style="width:160px;">
      <label>Duración</label><br>
      <input name="duration_label" value="{{ old('duration_label', $tutorial->duration_label) }}" maxlength="20" style="width:100%;" />
    </div>
  </div>

  <div style="margin-bottom:12px;">
    <label>Resumen</label><br>
    <input name="excerpt" value="{{ old('excerpt', $tutorial->excerpt) }}" maxlength="300" style="width:100%;" />
  </div>

  <div style="margin-bottom:12px;">
    <label>Descripción</label><br>
    <textarea name="body" rows="6" style="width:100%;">{{ old('body', $tutorial->body) }}</textarea>
  </div>

  <div style="margin-bottom:12px;">
    <label>Imagen (URL)</label><br>
    <input name="cover_image_url" value="{{ old('cover_image_url', $tutorial->cover_image_url) }}" maxlength="2000" style="width:100%;" />
  </div>

  <div style="margin-bottom:12px;">
    <label>YouTube URL (NO se muestra al cliente)</label><br>
    <input name="youtube_url" value="{{ old('youtube_url', $tutorial->youtube_url) }}" required maxlength="2000" style="width:100%;" />
  </div>

  <div style="margin-bottom:12px;">
    <label>Estado</label><br>
    <select name="status" required>
      <option value="draft" @selected(old('status', $tutorial->status)==='draft')>draft</option>
      <option value="published" @selected(old('status', $tutorial->status)==='published')>published</option>
    </select>
  </div>

  <button type="submit">Guardar cambios</button>
  <a href="{{ route('equipo.tutorials.index') }}">Volver</a>
</form>

<hr style="margin:18px 0;">
<div style="font-size:12px; opacity:.7;">
  Slug: <b>{{ $tutorial->slug }}</b> · Publicado: {{ optional($tutorial->published_at)->format('Y-m-d H:i') ?? '—' }}
</div>
@endsection
