@extends('layouts.app-equipo')

@section('title', 'Nueva Noticia')
@section('topbar_subtitle', 'Noticias')

@section('content')
<div class="page">

  <div class="top">
    <div>
      <h1>Nueva noticia</h1>
      <div class="sub">Publica comunicados y material informativo para clientes</div>
    </div>

    <a href="{{ route('equipo.noticias.index') }}">Volver</a>
  </div>

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
    $categorias = ['tributario','contable','laboral','finanzas','aviso'];
  @endphp

  <section class="panel">
    <div style="padding:16px;">

      <form method="POST" action="{{ route('equipo.noticias.store') }}">
        @csrf

        <div style="margin-bottom:12px;">
          <label><b>Título</b></label><br>
          <input name="titulo" value="{{ old('titulo') }}" required maxlength="190" style="width:100%;">
          @error('titulo') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
          <div style="min-width:240px;">
            <label><b>Categoría</b></label><br>
            <select name="categoria" required>
              @foreach($categorias as $c)
                <option value="{{ $c }}" @selected(old('categoria','tributario') === $c)>{{ ucfirst($c) }}</option>
              @endforeach
            </select>
            @error('categoria') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
          </div>

          <div style="min-width:240px;">
            <label><b>Estado</b></label><br>
            <select name="estado" required>
              <option value="borrador" @selected(old('estado','borrador')==='borrador')>borrador</option>
              <option value="publicado" @selected(old('estado')==='publicado')>publicado</option>
            </select>
            @error('estado') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
          </div>
        </div>

        <div style="margin-bottom:12px;">
          <label><b>Contenido</b></label><br>
          <textarea name="body" rows="10" required style="width:100%;">{{ old('body') }}</textarea>
          @error('body') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <button type="submit">Guardar</button>
        <a href="{{ route('equipo.noticias.index') }}" style="margin-left:10px;">Cancelar</a>
      </form>

    </div>
  </section>

</div>
@endsection
