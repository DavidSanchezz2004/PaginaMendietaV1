@extends('layouts.app-equipo')

@section('title', 'Editar operador')
@section('topbar_subtitle', 'Operadores')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/operadores.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Editar operador</h1>
      <div class="sub">{{ $operador->username }}</div>
    </div>
    <div class="actions">
      <a class="btn" href="{{ route('equipo.operadores.index') }}">Volver</a>
    </div>
  </div>

  <section class="panel">
    <form method="POST" action="{{ route('equipo.operadores.update', $operador) }}" class="form">
      @csrf
      @method('PUT')

      <div class="grid">
        <div class="field">
          <label>Username</label>
          <input name="username" value="{{ old('username', $operador->username) }}" autocomplete="off" />
          @error('username')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Estado</label>
          <select name="status">
            <option value="activo" {{ old('status', $operador->status)==='activo'?'selected':'' }}>activo</option>
            <option value="inactivo" {{ old('status', $operador->status)==='inactivo'?'selected':'' }}>inactivo</option>
          </select>
          @error('status')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
          <label>Nuevo password (opcional)</label>
          <input name="password" type="password" placeholder="dejar vacío para no cambiar" autocomplete="new-password" />
          @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Tipo --}}
        <div class="field">
          <label>Tipo</label>
          <select name="type" id="op_type">
            <option value="equipo" {{ old('type', $operador->type ?? 'equipo')==='equipo'?'selected':'' }}>equipo</option>
            <option value="cliente" {{ old('type', $operador->type ?? 'equipo')==='cliente'?'selected':'' }}>cliente</option>
          </select>
          <div class="hint">Si es <b>cliente</b>, podrá registrar empresas según plan/límite.</div>
          @error('type')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Suscripción --}}
        <div class="field">
          <label>Suscripción</label>
          <select name="subscription_status">
            <option value="active" {{ old('subscription_status', $operador->subscription_status ?? 'active')==='active'?'selected':'' }}>active</option>
            <option value="overdue" {{ old('subscription_status', $operador->subscription_status ?? '')==='overdue'?'selected':'' }}>overdue</option>
            <option value="suspended" {{ old('subscription_status', $operador->subscription_status ?? '')==='suspended'?'selected':'' }}>suspended</option>
          </select>
          @error('subscription_status')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Plan (solo cliente) --}}
        <div class="field js-client-only" id="wrap_plan">
          <label>Plan</label>
          @php($planVal = old('plan', $operador->plan ?? 'starter'))
          <select name="plan" id="op_plan">
            <option value="starter" {{ $planVal==='starter'?'selected':'' }}>starter (2 empresas)</option>
            <option value="oro" {{ $planVal==='oro'?'selected':'' }}>oro (3 empresas)</option>
            <option value="pro" {{ $planVal==='pro'?'selected':'' }}>pro (5 empresas)</option>
            <option value="empresa" {{ $planVal==='empresa'?'selected':'' }}>empresa (20 empresas)</option>
          </select>
          @error('plan')<div class="err">{{ $message }}</div>@enderror
        </div>

        {{-- NUEVO: Override (solo cliente) --}}
        <div class="field js-client-only" id="wrap_max">
          <label>Máx. empresas (override opcional)</label>
          <input name="max_companies" id="op_max" type="number" min="1" max="200"
                 value="{{ old('max_companies', $operador->max_companies) }}"
                 placeholder="Vacío = usa el plan" />
          @error('max_companies')<div class="err">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="actions-row">
        <button class="btn primary" type="submit">Actualizar</button>
      </div>
    </form>
  </section>
</div>

<script>
  (function(){
    const type = document.getElementById('op_type');
    const clientEls = document.querySelectorAll('.js-client-only');

    function toggle() {
      const isClient = (type && type.value === 'cliente');
      clientEls.forEach(el => el.style.display = isClient ? '' : 'none');

      // Si pasa a equipo, limpiamos override (no aplica)
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
