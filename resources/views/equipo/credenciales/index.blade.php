@extends('layouts.app-equipo')

@section('title', 'Credenciales')
@section('topbar_subtitle', 'Credenciales')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/credenciales.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Credenciales</h1>
      <div class="sub">Gestión por empresa y portal (rotación segura)</div>
    </div>

    <form class="search" method="GET" action="{{ route('equipo.credenciales.index') }}">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Buscar por RUC o razón social..." />
      <button class="btn" type="submit">Buscar</button>
    </form>
  </div>

  @if(session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Portales</h3>
        <p class="panel-sub">Mostrando {{ $accounts->total() }} registro(s).</p>
      </div>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Empresa</th>
            <th>RUC</th>
            <th>Portal</th>
            <th>Estado</th>
            <th>Última rotación</th>
            <th style="width:120px;">Acción</th>
          </tr>
        </thead>

        <tbody>
          @forelse($accounts as $a)
            <tr>
              <td class="tmain">{{ $a->company?->razon_social ?? '—' }}</td>
              <td class="mono">{{ $a->company?->ruc ?? '—' }}</td>
              <td>{{ strtoupper($a->portal) }}</td>

              <td>
                <span class="status {{ $a->status === 'active' ? 'ok' : 'off' }}">
                  {{ $a->status }}
                </span>
              </td>

              <td>
                {{ $a->latestCredential?->created_at?->format('Y-m-d H:i') ?? '—' }}
              </td>

              <td class="actions-cell">
                <a class="btn small"
                  href="{{ route('equipo.credenciales.create', $a) }}">
                  {{ $a->latestCredential ? 'Rotar' : 'Configurar' }}
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" style="padding:18px;">Aún no hay portales.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pager">
      {{ $accounts->links() }}
    </div>
  </section>
</div>
@endsection
