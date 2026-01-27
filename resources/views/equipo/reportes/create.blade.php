@extends('layouts.app-equipo')

@section('title', 'Nuevo Reporte')
@section('topbar_subtitle', 'Reportes')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/reporte_crear.css') }}">
@endpush

@section('content')
<h1>Nuevo Reporte</h1>

@if ($errors->any())
  <div style="padding:12px; margin:12px 0; border:1px solid #f3b4b4; background:#fff5f5; border-radius:10px;">
    <strong>Revisa el formulario:</strong>
    <ul style="margin:8px 0 0 18px;">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('equipo.reportes.store') }}">
  @csrf

  <div style="margin-bottom:12px;">
    <label>Empresa</label><br>
    <select name="company_id" required>
      <option value="">— Seleccionar —</option>

      @foreach($companies as $c)
        <option value="{{ $c->id }}" @selected((int)old('company_id') === (int)$c->id)>
          {{ $c->razon_social }} ({{ $c->ruc }})
        </option>
      @endforeach
    </select>

    @error('company_id') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Título</label><br>
    <input name="titulo" value="{{ old('titulo') }}" required />
    @error('titulo') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="display:flex; gap:10px; margin-bottom:12px;">
    <div>
      <label>Mes</label><br>
      <input name="periodo_mes" type="number" min="1" max="12" value="{{ old('periodo_mes') }}" />
      @error('periodo_mes') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
    </div>
    <div>
      <label>Año</label><br>
      <input name="periodo_anio" type="number" min="2000" max="2100" value="{{ old('periodo_anio') }}" />
      @error('periodo_anio') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
    </div>
  </div>

  <div style="margin-bottom:12px;">
    <label>Estado</label><br>
    <select name="estado" required>
      <option value="borrador" @selected(old('estado','borrador')==='borrador')>borrador</option>
      <option value="publicado" @selected(old('estado')==='publicado')>publicado</option>
    </select>
    @error('estado') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Power BI URL (Publish to Web)</label><br>
    <textarea name="powerbi_url_actual" rows="4" required>{{ old('powerbi_url_actual') }}</textarea>
    <small>Este link NUNCA se muestra al cliente en frontend. Solo se usa para redirect del backend.</small>
    @error('powerbi_url_actual') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Nota interna (opcional)</label><br>
    <textarea name="nota_interna" rows="3">{{ old('nota_interna') }}</textarea>
    @error('nota_interna') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <button type="submit">Guardar</button>
  <a href="{{ route('equipo.reportes.index') }}">Volver</a>
</form>
@endsection
