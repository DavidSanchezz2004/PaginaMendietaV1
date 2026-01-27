@extends('layouts.app-equipo')

@section('title', 'Portales - Empresa')
@section('topbar_subtitle', 'Empresas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/portales.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Portales activos</h1>
      <div class="sub">
        {{ $company->razon_social }} — <span class="mono">{{ $company->ruc }}</span>
      </div>
    </div>

    <div class="actions">
      <a class="btn" href="{{ route('equipo.empresas.show', $company) }}">Volver</a>
    </div>
  </div>

  <section class="panel" style="max-width:720px;">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Activar / desactivar portales</h3>
        <p class="panel-sub">Requiere MFA reciente (10 min). Esto controla qué aparece en Asignaciones.</p>
      </div>
    </div>

    <form method="POST" action="{{ route('equipo.empresas.portales.update', $company) }}" style="padding:18px;">
      @csrf

      <label style="display:block; margin-bottom:10px;">
        <input type="checkbox" name="portals[]" value="sunat" {{ ($states['sunat'] ?? false) ? 'checked' : '' }}>
        <b>SUNAT</b>
      </label>

      <label style="display:block; margin-bottom:10px;">
        <input type="checkbox" name="portals[]" value="sunafil" {{ ($states['sunafil'] ?? false) ? 'checked' : '' }}>
        <b>SUNAFIL</b>
      </label>

      <label style="display:block; margin-bottom:16px;">
        <input type="checkbox" name="portals[]" value="afp" {{ ($states['afp'] ?? false) ? 'checked' : '' }}>
        <b>AFP</b>
      </label>

      <button class="btn primary" type="submit">Guardar portales</button>
    </form>
  </section>
</div>
@endsection
