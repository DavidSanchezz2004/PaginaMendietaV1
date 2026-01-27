@extends('layouts.app-equipo')

@section('title', 'Detalle de Reporte')
@section('topbar_subtitle', 'Reportes y Documentos')

@section('content')
<div class="page">
  <div class="top" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
    <div>
      <h1>{{ $reporte->titulo }}</h1>
      <div class="sub">
        Empresa: <b>{{ $reporte->company?->razon_social ?? '—' }}</b>
        · RUC: <b>{{ $reporte->company?->ruc ?? '—' }}</b>
      </div>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ route('equipo.reportes.edit', $reporte) }}">Editar</a>
      <a href="{{ route('equipo.reportes.index') }}">Volver</a>
    </div>
  </div>

  @if(session('ok'))
    <div style="padding:10px; border:1px solid #ddd; border-radius:10px; margin:10px 0;">
      {{ session('ok') }}
    </div>
  @endif

  @if(session('error'))
    <div style="padding:10px; margin:10px 0; border:1px solid #f3b4b4; background:#fff5f5; border-radius:10px;">
      {{ session('error') }}
    </div>
  @endif

  <hr style="margin:14px 0;">

  <div style="display:grid; gap:10px; max-width:860px;">
    <div>
      <b>Estado:</b> {{ $reporte->estado }}
    </div>

    <div>
      <b>Periodo:</b>
      @php
        $pm = $reporte->periodo_mes ? str_pad((string)$reporte->periodo_mes, 2, '0', STR_PAD_LEFT) : '—';
        $py = $reporte->periodo_anio ?? '—';
      @endphp
      {{ $pm }} / {{ $py }}
    </div>

    @if(!empty($reporte->nota_interna))
      <div>
        <b>Nota interna:</b><br>
        <div style="white-space:pre-wrap;">{{ $reporte->nota_interna }}</div>
      </div>
    @endif

    <div>
      <b>Link Power BI (interno):</b><br>
      <div style="font-size:13px; opacity:.85; word-break:break-all;">
        {{ $reporte->powerbi_url_actual }}
      </div>
      <div style="margin-top:8px;">
        <a href="{{ $reporte->powerbi_url_actual }}" target="_blank" rel="noopener">
          Abrir Power BI
        </a>
      </div>
    </div>
  </div>

  <hr style="margin:14px 0;">

  {{-- Toggle portal de la empresa (si quieres mantenerlo aquí) --}}
  @if($reporte->company)
    <form method="POST" action="{{ route('equipo.empresas.portal.toggle', $reporte->company) }}">
      @csrf
      <button type="submit">
        {{ $reporte->company->portal_reportes_enabled ? 'Deshabilitar portal' : 'Habilitar portal' }}
      </button>
    </form>
  @endif

</div>
@endsection
