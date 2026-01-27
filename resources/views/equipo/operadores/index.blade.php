@extends('layouts.app-equipo')

@section('title', 'Operadores')
@section('topbar_subtitle', 'Operadores')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/operadores.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Operadores</h1>
      <div class="sub">Usuarios que SOLO usan la app (Tauri)</div>
    </div>

    <div class="actions">
      <a class="btn primary" href="{{ route('equipo.operadores.create') }}">Nuevo usuario app</a>
    </div>
  </div>

  @if(session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Lista</h3>
        <p class="panel-sub">Mostrando {{ $items->total() }} usuario(s).</p>
      </div>

      <form class="search" method="GET" action="{{ route('equipo.operadores.index') }}">
        <input name="q" value="{{ $q }}" placeholder="Buscar por username, tipo, plan o estado..." />
        <button class="btn" type="submit">Buscar</button>
      </form>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Tipo</th>
            <th>Plan</th>
            <th>Suscripción</th>
            <th>Estado</th>
            <th>Último login</th>
            <th style="width:140px;">Acciones</th>
          </tr>
        </thead>

        <tbody>
          @forelse($items as $op)
            @php
              $type = $op->type ?? 'equipo';
              $plan = $op->plan ?? 'starter';
              $sub  = $op->subscription_status ?? 'active';
              $isClient = $type === 'cliente';
            @endphp

            <tr>
              <td class="mono">{{ $op->id }}</td>

              <td class="tmain">
                {{ $op->username }}
                @if($isClient && !empty($op->max_companies))
                  <div class="muted">Límite override: <span class="mono">{{ $op->max_companies }}</span></div>
                @endif
              </td>

              <td>
                <span class="status {{ $isClient ? 'warn' : 'ok' }}">
                  {{ $type }}
                </span>
              </td>

              <td>
                @if($isClient)
                  <span class="badge">{{ $plan }}</span>
                @else
                  <span class="muted">—</span>
                @endif
              </td>

              <td>
                <span class="status
                  {{ $sub === 'active' ? 'ok' : ($sub === 'overdue' ? 'warn' : 'off') }}">
                  {{ $sub }}
                </span>
              </td>

              <td>
                <span class="status {{ $op->status === 'activo' ? 'ok' : 'off' }}">
                  {{ $op->status }}
                </span>
              </td>

              <td>{{ $op->last_login_at?->format('Y-m-d H:i') ?? '—' }}</td>

              <td class="actions-cell">
                <a class="icon-btn" href="{{ route('equipo.operadores.edit', $op) }}" title="Editar">
                  <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                  </svg>
                </a>

                <form method="POST" action="{{ route('equipo.operadores.destroy', $op) }}" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button class="icon-btn danger" type="submit"
                    onclick="return confirm('¿Eliminar usuario {{ $op->username }}?')"
                    title="Eliminar">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                      <path d="M3 6h18"></path>
                      <path d="M8 6V4h8v2"></path>
                      <path d="M6 6l1 16h10l1-16"></path>
                      <path d="M10 11v6"></path>
                      <path d="M14 11v6"></path>
                    </svg>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" style="padding:18px;">Aún no hay usuarios.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pager">{{ $items->links() }}</div>
  </section>
</div>
@endsection
