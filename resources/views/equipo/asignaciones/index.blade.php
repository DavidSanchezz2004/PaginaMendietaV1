@extends('layouts.app-equipo')

@section('title', 'Asignaciones')
@section('topbar_subtitle', 'Asignaciones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/asignaciones.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Asignaciones</h1>
      <div class="sub">Operadores ↔ Empresas/Portales</div>
    </div>

    <div class="actions">
      <a class="btn primary" href="{{ route('equipo.asignaciones.create') }}">Nueva asignación</a>
    </div>
  </div>

  @if(session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Listado</h3>
        <p class="panel-sub">Mostrando {{ $assignments->total() }} registro(s).</p>
      </div>

      <form class="search" method="GET" action="{{ route('equipo.asignaciones.index') }}">
        <input name="q" value="{{ $q }}" placeholder="Buscar por operador, RUC o razón social..." />
        <button class="btn" type="submit">Buscar</button>
      </form>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Operador</th>
            <th>Empresa</th>
            <th>Portal</th>
            <th>Estado</th>
            <th style="width:150px;">Acciones</th>
          </tr>
        </thead>

        <tbody>
          @forelse($assignments as $a)
            @php
              $op = $a->appUser;
              $acc = $a->portalAccount;
              $co = $acc?->company;
            @endphp

            <tr>
              <td data-label="Operador" class="tmain">
                <div class="stack">
                  <div class="mono">{{ $op?->username ?? '—' }}</div>
                  <div class="muted">Estado: {{ $op?->status ?? '—' }}</div>
                </div>
              </td>

              <td data-label="Empresa">
                <div class="stack">
                  <div class="mono">{{ $co?->ruc ?? '—' }}</div>
                  <div class="muted">{{ $co?->razon_social ?? '—' }}</div>
                </div>
              </td>

              <td data-label="Portal">
                <span class="pill">{{ strtoupper($acc?->portal ?? '—') }}</span>
              </td>

              <td data-label="Estado">
                <span class="status {{ $a->active ? 'ok' : 'off' }}">
                  {{ $a->active ? 'Activa' : 'Inactiva' }}
                </span>
              </td>

              <td class="actions-cell">
                {{-- Toggle --}}
                <form method="POST" action="{{ route('equipo.asignaciones.toggle', $a) }}" style="display:inline">
                  @csrf
                  @method('PATCH')
                  <button class="icon-btn" type="submit" title="{{ $a->active ? 'Desactivar' : 'Activar' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                      <path d="M12 6v6l4 2"></path>
                      <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                  </button>
                </form>

                {{-- Delete (confirm simple) --}}
                <form method="POST" action="{{ route('equipo.asignaciones.destroy', $a) }}" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button class="icon-btn danger" type="submit"
                    onclick="return confirm('¿Eliminar asignación? Operador: {{ $op?->username }} | Empresa: {{ $co?->ruc }} | Portal: {{ $acc?->portal }}')"
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
            <tr>
              <td colspan="5" style="padding:18px;">Aún no hay asignaciones.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pager">
      {{ $assignments->links() }}
    </div>
  </section>
</div>
@endsection
