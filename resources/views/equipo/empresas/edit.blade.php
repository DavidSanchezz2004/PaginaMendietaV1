@extends('layouts.app-equipo')

@section('title', 'Editar empresa')
@section('topbar_subtitle', 'Empresas')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/empresas_registrar.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Editar empresa</h1>
      <div class="sub">Empresas · Portal Mendieta</div>
    </div>

    <div class="actions">
      <a class="btn" href="{{ route('equipo.empresas.index') }}">Volver</a>
      <button class="btn primary" type="submit" form="form-company">Guardar cambios</button>
    </div>
  </div>

  @if(session('ok'))
    <div style="margin: 10px 0; padding: 10px 12px; border:1px solid rgba(5,61,56,.22); background: rgba(5,61,56,.06); border-radius:12px; font-weight:800;">
      {{ session('ok') }}
    </div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Datos de la empresa</h3>
        <p class="panel-sub">Edita la información y guarda los cambios.</p>
      </div>
    </div>

    <div class="panel-body">
      <form id="form-company" class="form" method="POST" action="{{ route('equipo.empresas.update', $company) }}" autocomplete="off">
        @csrf
        @method('PUT')

        <!-- Razón social -->
        <div class="field">
          <label class="lbl">Razón social <span class="req">*</span></label>
          <input class="inp" type="text" name="razon_social"
                 value="{{ old('razon_social', $company->razon_social) }}">
          @error('razon_social')<p class="help" style="color:#b91c1c;font-weight:800">{{ $message }}</p>@enderror
        </div>

        <!-- RUC -->
        <div class="field">
          <label class="lbl">RUC <span class="req">*</span></label>
          <input class="inp mono" type="text" name="ruc" maxlength="11" inputmode="numeric"
                 value="{{ old('ruc', $company->ruc) }}">
          @error('ruc')<p class="help" style="color:#b91c1c;font-weight:800">{{ $message }}</p>@enderror
        </div>

        <!-- Correo -->
        <div class="field">
          <label class="lbl">Correo principal</label>
          <input class="inp" type="email" name="correo_principal"
                 value="{{ old('correo_principal', $company->correo_principal) }}">
          @error('correo_principal')<p class="help" style="color:#b91c1c;font-weight:800">{{ $message }}</p>@enderror
        </div>

        <!-- Teléfono -->
        <div class="field">
          <label class="lbl">Teléfono</label>
          <input class="inp" type="text" name="telefono"
                 value="{{ old('telefono', $company->telefono) }}">
          @error('telefono')<p class="help" style="color:#b91c1c;font-weight:800">{{ $message }}</p>@enderror
        </div>

        <!-- Departamento -->
        <div class="field">
          <label class="lbl">Departamento</label>
          <input class="inp" type="text" name="departamento"
                 value="{{ old('departamento', $company->departamento) }}">
        </div>

        <!-- Provincia -->
        <div class="field">
          <label class="lbl">Provincia</label>
          <input class="inp" type="text" name="provincia"
                 value="{{ old('provincia', $company->provincia) }}">
        </div>

        <!-- Distrito -->
        <div class="field">
          <label class="lbl">Distrito</label>
          <input class="inp" type="text" name="distrito"
                 value="{{ old('distrito', $company->distrito) }}">
        </div>

        <!-- Dirección -->
        <div class="field">
          <label class="lbl">Dirección fiscal</label>
          <input class="inp" type="text" name="direccion_fiscal"
                 value="{{ old('direccion_fiscal', $company->direccion_fiscal) }}">
        </div>

        <!-- Ubigeo -->
        <div class="field">
          <label class="lbl">Ubigeo</label>
          <input class="inp mono" type="text" name="ubigeo"
                 value="{{ old('ubigeo', $company->ubigeo) }}">
        </div>

        <!-- Estado SUNAT -->
        <div class="field">
          <label class="lbl">Estado SUNAT</label>
          <input class="inp" type="text" name="sunat_estado"
                 value="{{ old('sunat_estado', $company->sunat_estado) }}">
        </div>

        <!-- Condición SUNAT -->
        <div class="field">
          <label class="lbl">Condición SUNAT</label>
          <input class="inp" type="text" name="sunat_condicion"
                 value="{{ old('sunat_condicion', $company->sunat_condicion) }}">
        </div>

        <div class="hr"></div>

        <!-- Estado interno -->
        <div class="field">
          <label class="lbl">Estado interno</label>
          @php $ei = old('estado_interno', $company->estado_interno); @endphp
          <select class="inp" name="estado_interno">
            <option value="Activo" @selected($ei==='Activo')>Activo</option>
            <option value="Pendiente" @selected($ei==='Pendiente')>Pendiente</option>
            <option value="Inactivo" @selected($ei==='Inactivo')>Inactivo</option>
          </select>
        </div>

        <!-- Asignación -->
        <div class="field">
          <label class="lbl">Asignar a trabajador</label>
          @php $au = old('assigned_user_id', $company->assigned_user_id); @endphp
          <select class="inp" name="assigned_user_id">
            <option value="">Sin asignar</option>
            @foreach($workers as $w)
              <option value="{{ $w->id }}" @selected((string)$au === (string)$w->id)>
                {{ $w->name }} ({{ str_replace('_',' ', $w->rol) }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Notas -->
        <div class="field full">
          <label class="lbl">Notas internas</label>
          <textarea class="inp ta" rows="4" name="notas_internas">{{ old('notas_internas', $company->notas_internas) }}</textarea>
        </div>

        <!-- Botones mobile -->
        <div class="form-actions">
          <a class="btn" href="{{ route('equipo.empresas.index') }}">Volver</a>
          <button class="btn primary" type="submit">Guardar cambios</button>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
