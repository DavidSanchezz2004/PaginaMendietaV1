@extends('layouts.app-equipo')

@section('title', 'Ver empresa')
@section('topbar_subtitle', 'Empresas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/empresas_mostrar.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>{{ $company->razon_social }}</h1>
      <div class="sub mono">{{ $company->ruc }}</div>
    </div>

    <div class="actions">
      <a class="btn" href="{{ route('equipo.empresas.index') }}">Volver</a>
      <a class="btn" href="{{ route('equipo.empresas.portales.edit', $company) }}">
  Portales
</a>

    </div>
  </div>

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Detalle</h3>
        <p class="panel-sub">Información principal y SUNAT.</p>
      </div>
    </div>

    <div class="panel-body">
      <div class="grid2">
        <div class="kv"><span class="k">Dirección</span><span class="v">{{ $company->direccion_fiscal ?? '—' }}</span></div>
        <div class="kv"><span class="k">Ubigeo</span><span class="v mono">{{ $company->ubigeo ?? '—' }}</span></div>
        <div class="kv"><span class="k">Departamento</span><span class="v">{{ $company->departamento ?? '—' }}</span></div>
        <div class="kv"><span class="k">Provincia</span><span class="v">{{ $company->provincia ?? '—' }}</span></div>
        <div class="kv"><span class="k">Distrito</span><span class="v">{{ $company->distrito ?? '—' }}</span></div>

        <div class="kv"><span class="k">Estado SUNAT</span><span class="v">{{ $company->sunat_estado ?? '—' }}</span></div>
        <div class="kv"><span class="k">Condición</span><span class="v">{{ $company->sunat_condicion ?? '—' }}</span></div>

        <div class="kv"><span class="k">Estado interno</span><span class="v">{{ $company->estado_interno }}</span></div>
        <div class="kv"><span class="k">Asignado</span><span class="v">{{ $company->assignedUser?->name ?? '—' }}</span></div>
      </div>

      <div class="notes">
        <div class="notes-label">Notas internas</div>
        <div class="box">{{ $company->notas_internas ?? '—' }}</div>
      </div>
    </div>
  </section>
</div>
@endsection
