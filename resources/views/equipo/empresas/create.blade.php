@extends('layouts.app-equipo')

@section('title', 'Registrar empresa')
@section('topbar_subtitle', 'Empresas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/empresas_registrar.css') }}">
@endpush

@section('content')

<div class="page">
  <div class="top">
    <div>
      <h1>Registrar empresa</h1>
      <div class="sub">Empresas · Portal Mendieta</div>
    </div>

    <div class="actions">
      <button class="btn" type="button" onclick="history.back()">Cancelar</button>
      <button class="btn primary" type="submit" form="empresaForm">Guardar empresa</button>
    </div>
  </div>

  @if(session('ok'))
    <div style="margin:12px 0;padding:12px 14px;border:1px solid rgba(5,61,56,.22);background:rgba(5,61,56,.06);border-radius:12px;font-weight:700;">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div style="margin:12px 0;padding:12px 14px;border:1px solid rgba(220,38,38,.25);background:rgba(220,38,38,.06);border-radius:12px;">
      <div style="font-weight:900;margin-bottom:6px;">Revisa estos campos:</div>
      <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $e)
          <li style="font-weight:700;">{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Datos de la empresa</h3>
        <p class="panel-sub">Completa la información para registrar una nueva empresa.</p>
      </div>
    </div>

    <div class="panel-body">
      <form id="empresaForm" class="form" method="POST" action="{{ route('equipo.empresas.store') }}" autocomplete="off">
        @csrf

        <!-- Razón social -->
        <div class="field">
          <label class="lbl">Razón social <span class="req">*</span></label>
          <input class="inp" id="razon_social" name="razon_social" type="text"
                 value="{{ old('razon_social') }}" placeholder="Ej: Empresa Mendieta SAC" required>
        </div>

        <!-- RUC -->
        <div class="field">
          <label class="lbl">RUC <span class="req">*</span></label>
          <input class="inp mono" id="ruc" name="ruc" type="text" inputmode="numeric"
                 maxlength="11" value="{{ old('ruc') }}" placeholder="11 dígitos" required>
          <p class="help" id="ruc_help">Escribe 11 dígitos para buscar automáticamente.</p>
        </div>

        <!-- Correo -->
        <div class="field">
          <label class="lbl">Correo principal</label>
          <input class="inp" id="correo_principal" name="correo_principal" type="email"
                 value="{{ old('correo_principal') }}" placeholder="ej: contacto@empresa.com">
        </div>

        <!-- Teléfono -->
        <div class="field">
          <label class="lbl">Teléfono</label>
          <input class="inp" id="telefono" name="telefono" type="text" inputmode="tel"
                 value="{{ old('telefono') }}" placeholder="+51 9xx xxx xxx">
        </div>

        <!-- Departamento -->
        <div class="field">
          <label class="lbl">Departamento</label>
          <select class="inp" id="departamento" name="departamento">
            <option value="">Seleccionar</option>
            <option value="LIMA" @selected(old('departamento')==='LIMA')>Lima</option>
            <option value="AREQUIPA" @selected(old('departamento')==='AREQUIPA')>Arequipa</option>
            <option value="LA LIBERTAD" @selected(old('departamento')==='LA LIBERTAD')>La Libertad</option>
          </select>
        </div>

        <!-- Provincia -->
        <div class="field">
          <label class="lbl">Provincia</label>
          <input class="inp" id="provincia" name="provincia" type="text"
                 value="{{ old('provincia') }}" placeholder="Ej: Lima">
        </div>

        <!-- Distrito -->
        <div class="field">
          <label class="lbl">Distrito</label>
          <input class="inp" id="distrito" name="distrito" type="text"
                 value="{{ old('distrito') }}" placeholder="Ej: Los Olivos">
        </div>

        <!-- Dirección fiscal -->
        <div class="field">
          <label class="lbl">Dirección fiscal</label>
          <input class="inp" id="direccion_fiscal" name="direccion_fiscal" type="text"
                 value="{{ old('direccion_fiscal') }}" placeholder="Av / Jr / Calle, N°">
        </div>

        <!-- Notas -->
        <div class="field full">
          <label class="lbl">Notas internas</label>
          <textarea class="inp ta" id="notas_internas" name="notas_internas" rows="4"
                    placeholder="Notas para el equipo contable...">{{ old('notas_internas') }}</textarea>
        </div>

        <div class="hr"></div>

        <!-- Estado interno -->
        <div class="field">
          <label class="lbl">Estado (interno)</label>
          <select class="inp" id="estado_interno" name="estado_interno" required>
            <option value="Activo" @selected(old('estado_interno','Activo')==='Activo')>Activo</option>
            <option value="Pendiente" @selected(old('estado_interno')==='Pendiente')>Pendiente</option>
            <option value="Inactivo" @selected(old('estado_interno')==='Inactivo')>Inactivo</option>
          </select>
        </div>

        <!-- Asignación -->
        <div class="field">
          <label class="lbl">Asignar a trabajador</label>
          <select class="inp" id="assigned_user_id" name="assigned_user_id">
            <option value="">Sin asignar</option>
            @foreach($workers as $w)
              <option value="{{ $w->id }}" @selected(old('assigned_user_id')==$w->id)>
                {{ $w->name }} ({{ str_replace('_',' ', $w->rol) }})
              </option>
            @endforeach
          </select>
        </div>

        {{-- =========================
           DATOS DEL CLIENTE (ACCESO)
           ========================= --}}
        <div class="hr"></div>

        <div class="field full">
          <div style="font-weight:900; font-size:14px; margin-bottom:6px;">
            Datos del cliente (acceso al portal)
          </div>
          <div style="opacity:.75; font-size:12.5px;">
            Se creará un usuario con rol <b>cliente</b> ligado a esta empresa.
          </div>
        </div>

        <!-- Nombre cliente -->
        <div class="field">
          <label class="lbl">Nombre del cliente <span class="req">*</span></label>
          <input class="inp" id="cliente_name" name="cliente_name" type="text"
                 value="{{ old('cliente_name') }}" placeholder="Ej: Juan Pérez" required>
        </div>

        <!-- Email cliente -->
        <div class="field">
          <label class="lbl">Email del cliente <span class="req">*</span></label>
          <input class="inp" id="cliente_email" name="cliente_email" type="email"
                 value="{{ old('cliente_email') }}" placeholder="Ej: cliente@gmail.com" required>
        </div>

        <!-- Password cliente -->
        <div class="field full">
          <label class="lbl">Contraseña temporal <span class="req">*</span></label>
          <input class="inp" id="cliente_password" name="cliente_password" type="password"
                 minlength="8" maxlength="100" value="{{ old('cliente_password') }}"
                 placeholder="Mínimo 8 caracteres" required>
          <p class="help" style="margin-top:6px;">
            Recomendado: una contraseña temporal y luego que el cliente la cambie.
          </p>
        </div>

        <!-- Hidden SUNAT -->
        <input type="hidden" id="sunat_estado" name="sunat_estado" value="{{ old('sunat_estado') }}">
        <input type="hidden" id="sunat_condicion" name="sunat_condicion" value="{{ old('sunat_condicion') }}">
        <input type="hidden" id="ubigeo" name="ubigeo" value="{{ old('ubigeo') }}">
        <input type="hidden" id="sunat_raw" name="sunat_raw" value="{{ old('sunat_raw') }}">

        <!-- Botones mobile -->
        <div class="form-actions">
          <button class="btn" type="button" onclick="history.back()">Cancelar</button>
          <button class="btn primary" type="submit">Guardar empresa</button>
        </div>

      </form>
    </div>
  </section>
</div>

@endsection

@push('scripts')
<script>
  const $ = (id) => document.getElementById(id);
  const ruc = $("ruc");
  const help = $("ruc_help");

  let timer = null;
  let lastRuc = null;

  function digits(s){ return (s||'').replace(/\D+/g,''); }

  function setHelp(text, ok=false){
    help.textContent = text;
    help.style.color = ok ? "rgba(5,61,56,.95)" : "rgba(15,23,42,.55)";
  }

  function clearSunat(){
    $("sunat_estado").value = '';
    $("sunat_condicion").value = '';
    $("ubigeo").value = '';
    $("sunat_raw").value = '';
  }

  async function lookupRuc(rucValue){
    setHelp("Buscando RUC en SUNAT...");
    clearSunat();

    try{
      const url = `{{ url('/equipo/empresas/ruc') }}/${encodeURIComponent(rucValue)}`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      const json = await res.json();

      if(!res.ok || !json.ok){
        setHelp(json.message || "No se pudo consultar el RUC.");
        return;
      }

      const d = json.data;

      $("razon_social").value = d.razon_social || '';
      $("direccion_fiscal").value = d.direccion_fiscal || '';
      $("provincia").value = d.provincia || '';
      $("distrito").value = d.distrito || '';

      if (d.departamento){
        $("departamento").value = d.departamento;
      }

      $("sunat_estado").value = d.sunat_estado || '';
      $("sunat_condicion").value = d.sunat_condicion || '';
      $("ubigeo").value = d.ubigeo || '';

      // guardamos respaldo completo del proveedor (raw)
      $("sunat_raw").value = JSON.stringify(json.raw || {});

      // badge textual
      setHelp(`SUNAT: ${d.sunat_estado || '-'} / ${d.sunat_condicion || '-'}`, true);

    } catch(e){
      setHelp("Error consultando el RUC.");
    }
  }

  ruc.addEventListener("input", () => {
    ruc.value = digits(ruc.value).slice(0, 11);
    clearTimeout(timer);

    if (ruc.value.length < 11){
      lastRuc = null;
      clearSunat();
      setHelp("Escribe 11 dígitos para buscar automáticamente.");
      return;
    }

    if (ruc.value.length === 11 && ruc.value !== lastRuc){
      timer = setTimeout(() => {
        lastRuc = ruc.value;
        lookupRuc(ruc.value);
      }, 250);
    }
  });
</script>
@endpush
