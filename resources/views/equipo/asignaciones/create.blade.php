@extends('layouts.app-equipo')

@section('title', 'Nueva asignación')
@section('topbar_subtitle', 'Asignaciones')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/asignaciones.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Nueva asignación</h1>
      <div class="sub">Vincula un operador con un portal de una empresa</div>
    </div>

    <div class="actions">
      <a class="btn" href="{{ route('equipo.asignaciones.index') }}">Volver</a>
    </div>
  </div>

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Formulario</h3>
        <p class="panel-sub">Requiere MFA reciente (10 min).</p>
      </div>
    </div>

    <form class="form" method="POST" action="{{ route('equipo.asignaciones.store') }}">
      @csrf

      <div class="grid">
        <div class="field">
          <label>Operador (AppUser)</label>
          <select name="app_user_id" required>
            <option value="">— Selecciona —</option>
            @foreach($operators as $op)
              <option value="{{ $op->id }}" @selected(old('app_user_id') == $op->id)>
                {{ $op->username }} ({{ $op->status }})
              </option>
            @endforeach
          </select>
          @error('app_user_id') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
          <label>Empresa + Portal (PortalAccount)</label>
          <select name="portal_account_id" required>
            <option value="">— Selecciona —</option>
            @foreach($accounts as $acc)
              @php $co = $acc->company; @endphp
              <option value="{{ $acc->id }}" @selected(old('portal_account_id') == $acc->id)>
                {{ $co?->ruc }} — {{ $co?->razon_social }} | {{ strtoupper($acc->portal) }} | {{ $acc->status }}
              </option>
            @endforeach
          </select>
          @error('portal_account_id') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="field">
          <label>Estado</label>
          <select name="active">
            <option value="1" @selected(old('active', '1') == '1')>Activa</option>
            <option value="0" @selected(old('active') == '0')>Inactiva</option>
          </select>
          @error('active') <div class="err">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-actions">
        <button class="btn primary" type="submit">Guardar asignación</button>
      </div>
    </form>
  </section>
</div>
@endsection
