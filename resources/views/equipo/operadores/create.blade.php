@extends('layouts.app-equipo')

@section('title', 'Nuevo operador')
@section('topbar_subtitle', 'Operadores')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/operadores.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Nuevo operador</h1>
      <div class="sub">Se usará para login en la app (Tauri)</div>
    </div>
    <div class="actions">
      <a class="btn" href="{{ route('equipo.operadores.index') }}">Volver</a>
    </div>
  </div>

  <section class="panel">
    <form method="POST" action="{{ route('equipo.operadores.store') }}" class="form">
      @csrf

      <div class="grid">
        <div class="field">
          <label>Username</label>
          <input name="username" value="{{ old('username') }}" placeholder="operador01" autocomplete="off" />
          @error('username')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Estado</label>
          <select name="status">
            <option value="activo" {{ old('status','activo')==='activo'?'selected':'' }}>activo</option>
            <option value="inactivo" {{ old('status')==='inactivo'?'selected':'' }}>inactivo</option>
          </select>
          @error('status')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Password</label>
          <input name="password" type="password" placeholder="mínimo 8" autocomplete="new-password" />
          @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Tipo --}}
        <div class="field">
          <label>Tipo</label>
          <select name="type" id="op_type">
            <option value="equipo" {{ old('type','equipo')==='equipo'?'selected':'' }}>equipo</option>
            <option value="cliente" {{ old('type')==='cliente'?'selected':'' }}>cliente</option>
          </select>
          <div class="hint">Si es <b>cliente</b>, podrá registrar sus empresas (según plan).</div>
          @error('type')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Estado de suscripción --}}
        <div class="field">
          <label>Suscripción</label>
          <select name="subscription_status">
            <option value="active" {{ old('subscription_status','active')==='active'?'selected':'' }}>active</option>
            <option value="overdue" {{ old('subscription_status')==='overdue'?'selected':'' }}>overdue</option>
            <option value="suspended" {{ old('subscription_status')==='suspended'?'selected':'' }}>suspended</option>
          </select>
          <div class="hint">Si no está <b>active</b>, se bloquea el registro de empresas (y/o ejecuciones).</div>
          @error('subscription_status')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Plan (solo cliente) --}}
        <div class="field js-client-only" id="wrap_plan">
          <label>Plan</label>
          <select name="plan" id="op_plan">
            <option value="starter" {{ old('plan','starter')==='starter'?'selected':'' }}>starter (2 empresas)</option>
            <option value="oro" {{ old('plan')==='oro'?'selected':'' }}>oro (3 empresas)</option>
            <option value="pro" {{ old('plan')==='pro'?'selected':'' }}>pro (5 empresas)</option>
            <option value="empresa" {{ old('plan')==='empresa'?'selected':'' }}>empresa (20 empresas)</option>
          </select>
          @error('plan')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Límite override (solo cliente) --}}
        <div class="field js-client-only" id="wrap_max">
          <label>Máx. empresas (override opcional)</label>
          <input name="max_companies" id="op_max" type="number" min="1" max="200"
                 value="{{ old('max_companies') }}"
                 placeholder="Vacío = usa el plan" />
          <div class="hint">Si lo dejas vacío, se usa el límite del plan.</div>
          @error('max_companies')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="actions-row">
        <button class="btn primary" type="submit">Guardar</button>
      </div>
    </form>
  </section>
</div>

{{-- Toggle simple --}}
<script>
  (function(){
    const type = document.getElementById('op_type');
    const clientEls = document.querySelectorAll('.js-client-only');

    function toggle() {
      const isClient = (type && type.value === 'cliente');
      clientEls.forEach(el => el.style.display = isClient ? '' : 'none');

      // Si NO es cliente, no enviamos override (evita guardar basura)
      if (!isClient) {
        const max = document.getElementById('op_max');
        if (max) max.value = '';
      }
    }
    if (type) {
      type.addEventListener('change', toggle);
      toggle();
    }
  })();
</script>
@endsection
