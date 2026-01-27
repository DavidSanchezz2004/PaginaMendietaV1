@extends('layouts.app-equipo')

@section('title', 'Editar Reporte')
@section('topbar_subtitle', 'Reportes')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
@endpush


@section('content')
<h1>Editar / Rotar Reporte</h1>

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

<form method="POST" action="{{ route('equipo.reportes.update', $reporte) }}">
  @csrf
  @method('PUT')

  <div style="margin-bottom:12px;">
    <label>Empresa</label><br>
    <select name="company_id" required>
      @foreach($companies as $c)
        @php
          $selectedCompany = old('company_id', $reporte->company_id);
        @endphp
        <option value="{{ $c->id }}" @selected((int)$selectedCompany === (int)$c->id)>
          {{ $c->razon_social }} ({{ $c->ruc }})
        </option>
      @endforeach
    </select>
    @error('company_id') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Título</label><br>
    <input name="titulo" value="{{ old('titulo', $reporte->titulo) }}" required />
    @error('titulo') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="display:flex; gap:10px; margin-bottom:12px;">
    <div>
      <label>Mes</label><br>
      <input name="periodo_mes" type="number" min="1" max="12"
             value="{{ old('periodo_mes', $reporte->periodo_mes) }}" />
      @error('periodo_mes') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
    </div>
    <div>
      <label>Año</label><br>
      <input name="periodo_anio" type="number" min="2000" max="2100"
             value="{{ old('periodo_anio', $reporte->periodo_anio) }}" />
      @error('periodo_anio') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
    </div>
  </div>

  <div style="margin-bottom:12px;">
    <label>Estado</label><br>
    <select name="estado" required>
      <option value="borrador" @selected(old('estado',$reporte->estado)==='borrador')>borrador</option>
      <option value="publicado" @selected(old('estado',$reporte->estado)==='publicado')>publicado</option>
    </select>
    @error('estado') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Power BI URL actual (rotación)</label><br>
    <textarea name="powerbi_url_actual" rows="4" required>{{ old('powerbi_url_actual', $reporte->powerbi_url_actual) }}</textarea>
    <small>Actualizar aquí es el “kill-switch”.</small>
    @error('powerbi_url_actual') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <div style="margin-bottom:12px;">
    <label>Nota interna</label><br>
    <textarea name="nota_interna" rows="3">{{ old('nota_interna', $reporte->nota_interna) }}</textarea>
    @error('nota_interna') <div style="color:#b00020; font-size:13px;">{{ $message }}</div> @enderror
  </div>

  <button type="submit">Actualizar</button>
  <a href="{{ route('equipo.reportes.index') }}">Volver</a>
</form>
@endsection
