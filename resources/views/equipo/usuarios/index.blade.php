@extends('layouts.app-equipo')

@section('title', 'Usuarios')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
    <div>
      <h2 style="margin:0;">Usuarios</h2>
      <div class="muted">Administrar accesos al portal</div>
    </div>
    <a class="btn btn-primary" href="{{ route('equipo.usuarios.create') }}">+ Nuevo</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success" style="margin-top:12px;">{{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger" style="margin-top:12px;">{{ session('error') }}</div>
  @endif

  <form method="GET" action="{{ route('equipo.usuarios.index') }}" style="margin-top:12px;display:flex;gap:8px;align-items:center;">
    <input class="input" type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, email o rol">
    <button class="btn" type="submit">Buscar</button>
    @if($q)
      <a class="btn" href="{{ route('equipo.usuarios.index') }}">Limpiar</a>
    @endif
  </form>

  <div style="overflow:auto;margin-top:12px;">
    <table class="table" style="width:100%;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Email verificado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->rol }}</td>
            <td>{{ $u->email_verified_at ? 'Sí' : 'No' }}</td>
            <td style="white-space:nowrap;">
              <a class="btn btn-sm" href="{{ route('equipo.usuarios.edit', $u) }}">Editar</a>

              <form method="POST" action="{{ route('equipo.usuarios.destroy', $u) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('¿Eliminar usuario {{ $u->email }}?');">
                  Eliminar
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6">Sin registros.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;">
    {{ $rows->links() }}
  </div>
</div>
@endsection
