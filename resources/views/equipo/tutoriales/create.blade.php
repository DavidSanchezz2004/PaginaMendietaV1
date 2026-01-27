@extends('layouts.app-equipo')

@section('title', 'Nuevo Tutorial')
@section('topbar_subtitle', 'Contenido')

@section('content')
<h1>Nuevo Tutorial</h1>

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

<form method="POST" action="{{ route('equipo.tutorials.store') }}">
  @csrf

  <div style="margin-bottom:12px;">
    <label>Título</label><br>
    <input name="title" value="{{ old('title') }}" required maxlength="190" style="width:100%;" />
    @error('title') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="display:flex; gap:10px; margin-bottom:12px;">
    <div style="flex:1;">
      <label>Categoría</label><br>
      <input name="category" value="{{ old('category','tributario') }}" required maxlength="40" style="width:100%;" />
      @error('category') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
    </div>

    <div style="width:160px;">
      <label>Duración (opcional)</label><br>
      <input name="duration_label" value="{{ old('duration_label') }}" maxlength="20" placeholder="12:30" style="width:100%;" />
      @error('duration_label') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
    </div>
  </div>

  <div style="margin-bottom:12px;">
    <label>Resumen (opcional)</label><br>
    <input name="excerpt" value="{{ old('excerpt') }}" maxlength="300" style="width:100%;" />
    @error('excerpt') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Descripción (opcional)</label><br>
    <textarea name="body" rows="6" style="width:100%;">{{ old('body') }}</textarea>
    @error('body') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Imagen (URL opcional)</label><br>
    <input name="cover_image_url" value="{{ old('cover_image_url') }}" maxlength="2000" style="width:100%;" />
    @error('cover_image_url') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>YouTube URL (NO se muestra al cliente)</label><br>
    <input name="youtube_url" value="{{ old('youtube_url') }}" required maxlength="2000" style="width:100%;" />
    <small>El cliente accederá por ruta interna /cliente/tutoriales/{id}/ver (redirect).</small>
    @error('youtube_url') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Estado</label><br>
    <select name="status" required>
      <option value="draft" @selected(old('status','draft')==='draft')>draft</option>
      <option value="published" @selected(old('status')==='published')>published</option>
    </select>
    @error('status') <div style="color:#b00020;font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <button type="submit">Guardar</button>
  <a href="{{ route('equipo.tutorials.index') }}">Volver</a>
</form>
@endsection
