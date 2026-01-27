@extends('layouts.app-equipo')

@section('title', 'Jobs / Resultados')
@section('topbar_subtitle', 'Jobs / Resultados')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/jobs.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Jobs / Resultados</h1>
      <div class="sub">Trazabilidad completa de ejecuciones por operador y portal.</div>
    </div>
  </div>

  <section class="panel">
    <div class="panel-head" style="display:flex; gap:12px; align-items:flex-end; justify-content:space-between;">
      <div>
        <h3 class="panel-title">Historial</h3>
        <p class="panel-sub">Mostrando {{ $jobs->total() }} registro(s).</p>
      </div>

      <form method="GET" action="{{ route('equipo.jobs.index') }}" style="display:flex; gap:10px; flex-wrap:wrap;">
        <input class="input" name="q" value="{{ $q }}" placeholder="Buscar: RUC, razón social, job_uid, operador, acción..." />

        <select class="input" name="portal">
          <option value="">Todos los portales</option>
          <option value="sunat"   @selected($portal==='sunat')>SUNAT</option>
          <option value="sunafil" @selected($portal==='sunafil')>SUNAFIL</option>
          <option value="afp"     @selected($portal==='afp')>AFP</option>
        </select>

        <select class="input" name="status">
          <option value="">Todos los estados</option>
          <option value="pending" @selected($status==='pending')>pending</option>
          <option value="running" @selected($status==='running')>running</option>
          <option value="done"    @selected($status==='done')>done</option>
          <option value="failed"  @selected($status==='failed')>failed</option>
        </select>

        <button class="btn primary" type="submit">Filtrar</button>
        <a class="btn" href="{{ route('equipo.jobs.index') }}">Limpiar</a>
      </form>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>Job</th>
            <th>Empresa</th>
            <th>Portal</th>
            <th>Acción</th>
            <th>Operador</th>
            <th>Estado</th>
            <th>Último resultado</th>
            <th style="width:72px;">Ver</th>
          </tr>
        </thead>
        <tbody>
          @forelse($jobs as $j)
          <tr>
            <td class="mono">
              {{ $j->job_uid ?? ('#'.$j->id) }}<br>
              <small class="muted">{{ $j->device_id }}</small>
            </td>

            <td>
              <div class="tmain">{{ $j->company?->razon_social ?? '—' }}</div>
              <small class="muted mono">{{ $j->company?->ruc ?? '—' }}</small>
            </td>

            <td>{{ strtoupper($j->portal) }}</td>

            <td class="mono">{{ $j->action }}</td>

            <td>{{ $j->appUser?->username ?? '—' }}</td>

            <td>
              <span class="status
                @if($j->status==='done') ok
                @elseif($j->status==='failed') off
                @elseif($j->status==='running') warn
                @else
                @endif
              ">
                {{ $j->status }}
              </span>
            </td>

            <td>
              @if($j->latestResult)
                <span class="status {{ $j->latestResult->ok ? 'ok' : 'off' }}">
                  {{ $j->latestResult->ok ? 'OK' : 'ERROR' }}
                </span>
                <small class="muted">{{ $j->latestResult->created_at?->format('Y-m-d H:i') }}</small>
              @else
                <span class="muted">—</span>
              @endif
            </td>

            <td class="actions-cell">
              <a class="icon-btn" href="{{ route('equipo.jobs.show', $j) }}" title="Ver">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" style="padding:18px;">Aún no hay jobs registrados.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pager">{{ $jobs->links() }}</div>
  </section>
</div>
@endsection
