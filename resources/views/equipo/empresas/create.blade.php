@extends('layouts.app-equipo')

@section('title', 'Registrar empresa')
@section('topbar_subtitle', 'Empresas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/empresas_registrar.css') }}">

<style>
  .err{
    margin-top:6px;
    font-size:12.5px;
    font-weight:800;
    color:#b91c1c;
  }
</style>
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

  {{-- MENSAJE OK --}}
  @if(session('ok'))
    <div style="margin:12px 0;padding:12px 14px;border:1px solid rgba(5,61,56,.22);background:rgba(5,61,56,.06);border-radius:12px;font-weight:700;">
      {{ session('ok') }}
    </div>
  @endif

  {{-- RESUMEN DE ERRORES --}}
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

        {{-- Razón social --}}
        <div class="field">
          <label class="lbl">Razón social <span class="req">*</span></label>
          <input class="inp" name="razon_social" value="{{ old('razon_social') }}" required>
          @error('razon_social') <div class="err">{{ $message }}</div> @enderror
        </div>

        {{-- RUC --}}
        <div class="field">
          <label class="lbl">RUC <span class="req">*</span></label>
          <input class="inp mono" name="ruc" maxlength="11" value="{{ old('ruc') }}" required>
          <p class="help" id="ruc_help">Escribe 11 dígitos para buscar automáticamente.</p>
          @error('ruc') <div class="err">{{ $message }}</div> @enderror
        </div>

        {{-- Correo principal --}}
        <div class="field">
          <label class="lbl">Correo principal</label>
          <input class="inp" name="correo_principal" type="email" value="{{ old('correo_principal') }}">
          @error('correo_principal') <div class="err">{{ $message }}</div> @enderror
        </div>

        <div class="hr"></div>

        {{-- DATOS DEL CLIENTE --}}
        <div class="field full">
          <strong>Datos del cliente (acceso al portal)</strong>
        </div>

        {{-- Nombre cliente --}}
        <div class="field">
          <label class="lbl">Nombre del cliente <span class="req">*</span></label>
          <input class="inp" name="cliente_name" value="{{ old('cliente_name') }}" required>
          @error('cliente_name') <div class="err">{{ $message }}</div> @enderror
        </div>

        {{-- Email cliente --}}
        <div class="field">
          <label class="lbl">Email del cliente <span class="req">*</span></label>
          <input class="inp" name="cliente_email" type="email" value="{{ old('cliente_email') }}" required>
          @error('cliente_email') <div class="err">{{ $message }}</div> @enderror
        </div>

        {{-- Password cliente --}}
        <div class="field full">
          <label class="lbl">Contraseña temporal <span class="req">*</span></label>
          <input class="inp" name="cliente_password" type="password" minlength="8" required>
          @error('cliente_password') <div class="err">{{ $message }}</div> @enderror
        </div>

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
