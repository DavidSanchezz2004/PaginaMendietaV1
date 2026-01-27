@extends('layouts.app-equipo')

@section('title', 'Noticias')
@section('topbar_subtitle', 'Noticias y Comunicados')

@section('content')
<div class="page">

  <div class="top">
    <div>
      <h1>Noticias</h1>
      <div class="sub">Comunicados contables, tributarios y anuncios internos</div>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <form method="GET" action="{{ route('equipo.noticias.index') }}"
            style="display:flex; gap:8px; align-items:center;">
        <input
          type="text"
          name="q"
          value="{{ $q ?? '' }}"
          placeholder="Buscar por título o contenido..."
        />
        <button type="submit">Buscar</button>

        @if(!empty($q))
          <a href="{{ route('equipo.noticias.index') }}" style="font-size:13px;">Limpiar</a>
        @endif
      </form>

      <a href="{{ route('equipo.noticias.create') }}" class="btn primary">
        Nueva noticia
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div style="padding:10px; margin:10px 0; border:1px solid #cce7d0; background:#f3fff6; border-radius:10px;">
      {{ session('ok') }}
    </div>
  @endif

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Título</th>
          <th>Categoría</th>
          <th>Estado</th>
          <th>Publicado</th>
          <th style="width:180px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $row)
          <tr>
            <td>{{ $row->title }}</td>
            <td>{{ ucfirst($row->category) }}</td>
            <td>
              <span class="status {{ $row->status === 'publicado' ? 'ok' : 'off' }}">
                {{ $row->status }}
              </span>
            </td>
            <td>{{ $row->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
            <td>
              <a href="{{ route('equipo.noticias.edit', $row) }}">Editar</a>

              <form method="POST"
                    action="{{ route('equipo.noticias.destroy', $row) }}"
                    style="display:inline;">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('¿Eliminar noticia?')">Eliminar</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="padding:18px;">No hay noticias registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pager">
    {{ $rows->links() }}
  </div>

</div>
@endsection
