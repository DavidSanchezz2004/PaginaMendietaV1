@extends('layouts.app-equipo')

@section('title','Confirmar MFA')
@section('topbar_subtitle','Seguridad')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mfa_confirm.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Confirmación MFA</h1>
      <div class="sub">Por seguridad, confirma tu código para continuar.</div>
    </div>
  </div>

  <section class="panel">
    <div class="panel-body">
      <form method="POST" action="{{ route('equipo.mfa.confirm.verify') }}">
        @csrf

        <label class="lbl">Código (Authenticator)</label>
        <input class="inp mono" name="code" inputmode="numeric" maxlength="8" autofocus>

        @error('code')
          <div style="margin-top:8px; color:#b91c1c; font-weight:700;">{{ $message }}</div>
        @enderror

        <div style="margin-top:14px; display:flex; gap:10px;">
          <a class="btn" href="{{ route('equipo.dashboard') }}">Cancelar</a>
          <button class="btn primary" type="submit">Confirmar</button>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
