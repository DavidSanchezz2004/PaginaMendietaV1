@extends('layouts.app-equipo')

@section('title', 'Detalle Job')
@section('topbar_subtitle', 'Jobs / Resultados')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/jobs.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Detalle del Job</h1>
      <div class="sub mono">{{ $job->job_uid ?? ('#'.$job->id) }}</div>
    </div>

    <div class="actions">
      <a class="btn" href="{{ route('equipo.jobs.index') }}">Volver</a>
    </div>
  </div>

  <section class="panel" style="margin-bottom:14px;">
    <div style="padding:18px; display:grid; gap:10px; grid-template-columns: 1fr 1fr;">
      <div>
        <div class="muted">Empresa</div>
        <div class="tmain">{{ $job->company?->razon_social ?? '—' }}</div>
        <div class="mono muted">{{ $job->company?->ruc ?? '—' }}</div>
      </div>

      <div>
        <div class="muted">Operador</div>
        <div class="tmain">{{ $job->appUser?->username ?? '—' }}</div>
        <div class="mono muted">{{ $job->device_id }}</div>
      </div>

      <div>
        <div class="muted">Portal / Acción</div>
        <div class="tmain">{{ strtoupper($job->portal) }}</div>
        <div class="mono muted">{{ $job->action }}</div>
      </div>

      <div>
        <div class="muted">Estado</div>
        <span class="status
          @if($job->status==='done') ok
          @elseif($job->status==='failed') off
          @elseif($job->status==='running') warn
          @endif
        ">
          {{ $job->status }}
        </span>
        <div class="muted">
          Inicio: {{ $job->started_at?->format('Y-m-d H:i') ?? '—' }} |
          Fin: {{ $job->finished_at?->format('Y-m-d H:i') ?? '—' }}
        </div>
      </div>
    </div>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Resultados</h3>
        <p class="panel-sub">Últimos primero.</p>
      </div>
    </div>

    <div style="padding:18px;">
      @forelse($job->results as $r)
        <div class="card" style="padding:14px; border:1px solid var(--line); border-radius:14px; margin-bottom:12px;">
          <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
            <div>
              <span class="status {{ $r->ok ? 'ok' : 'off' }}">{{ $r->ok ? 'OK' : 'ERROR' }}</span>
              <span class="muted" style="margin-left:10px;">{{ $r->created_at?->format('Y-m-d H:i:s') }}</span>
            </div>
          </div>

          @if(!$r->ok && $r->error)
            <div style="margin-top:10px;">
              <div class="muted">Error</div>
              <pre style="white-space:pre-wrap; margin:0;">{{ $r->error }}</pre>
            </div>
          @endif

          @if($r->data)
            <div style="margin-top:10px;">
              <div class="muted">Data (JSON)</div>
              <pre style="white-space:pre-wrap; margin:0;">{{ json_encode($r->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
          @endif

          @if($r->evidence)
            <div style="margin-top:10px;">
              <div class="muted">Evidencias</div>
              <pre style="white-space:pre-wrap; margin:0;">{{ json_encode($r->evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
          @endif
        </div>
      @empty
        <div class="muted">Este job aún no tiene resultados.</div>
      @endforelse
    </div>
  </section>
</div>
@endsection
