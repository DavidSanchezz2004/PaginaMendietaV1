@extends('layouts.app-equipo')

@section('title', 'Rotar credenciales')
@section('topbar_subtitle', 'Credenciales')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/credenciales.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Rotar credenciales</h1>
      <div class="sub">
        {{ $portalAccount->company?->razon_social }} — RUC {{ $portalAccount->company?->ruc }} — {{ strtoupper($portalAccount->portal) }}
      </div>
    </div>

    <div class="actions">
      <a class="btn" href="{{ route('equipo.credenciales.index') }}">Volver</a>
    </div>
  </div>

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Nueva credencial</h3>
        <p class="panel-sub">
          Esto crea una nueva versión. No mostramos ni guardamos el texto plano en la BD.
        </p>
      </div>
    </div>

    <div class="form-wrap">
      <form method="POST" action="{{ route('equipo.credenciales.store', $portalAccount) }}">
        @csrf

        <div class="grid">
          <div class="field">
            <label>Usuario (opcional)</label>
            <input name="username" value="{{ old('username') }}" placeholder="USUARIO_SOL" />
            @error('username')<div class="err">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label>Clave (obligatorio)</label>
            <input name="password" type="password" placeholder="••••••••" />
            @error('password')<div class="err">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="actions-row">
          <button class="btn primary danger" type="submit">Guardar y rotar</button>
          <div class="hint">* Requiere MFA reciente (10 min) por tus rutas.</div>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
