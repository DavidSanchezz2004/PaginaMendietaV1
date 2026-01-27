@extends('layouts.app-equipo')

@section('title', 'Tutoriales')
@section('topbar_subtitle', 'Contenido')

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Tutoriales</h1>
      <div class="sub">Guías y videos (YouTube) publicados en el portal</div>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <form method="GET" action="{{ route('equipo.tutorials.index') }}" style="display:flex; gap:8px;">
        <input name="q" value="{{ $q ?? '' }}" placeholder="Buscar por título..." />
        <button type="submit">Buscar</button>
        @if(!empty($q))
          <a href="{{ route('equipo.tutorials.index') }}">Limpiar</a>
        @endif
      </form>

      <a href="{{ route('equipo.tutorials.create') }}">Nuevo tutorial</a>
    </div>
  </div>

  @if(session('ok'))
    <div style="padding:10px; margin:10px 0; border:1px solid #ddd; border-radius:10px;">
      {{ session('ok') }}
    </div>
  @endif

  <div class="table-wrap">
    <table class="table" border="0" cellpadding="8" cellspacing="0">
      <thead>
        <tr>
          <th>Título</th>
          <th>Categoría</th>
          <th>Estado</th>
          <th>Publicado</th>
          <th style="width:220px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $t)
          <tr>
            <td>
              <div style="font-weight:600;">{{ $t->title }}</div>
              <div style="font-size:12px; opacity:.7;">
                slug: {{ $t->slug }}
                @if($t->duration_label) · {{ $t->duration_label }} @endif
              </div>
            </td>
            <td>{{ $t->category }}</td>
            <td>{{ $t->status }}</td>
            <td>{{ optional($t->published_at)->format('Y-m-d H:i') ?? '—' }}</td>
            <td>
              <a href="{{ route('equipo.tutorials.edit', $t) }}">Editar</a>
              &nbsp;|&nbsp;
              <form method="POST" action="{{ route('equipo.tutorials.destroy', $t) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Eliminar tutorial?')">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="padding:18px;">Aún no hay tutoriales.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pager">
    {{ $rows->links() }}
  </div>
</div>
@endsection
