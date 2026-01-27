@extends('layouts.app-equipo')

@section('title', 'Reportes')
@section('topbar_subtitle', 'Reportes y Documentos')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/reporte_listar.css') }}">
@endpush


@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Reportes</h1>
      <div class="sub">Links Power BI (Publish to Web) gestionados por empresa</div>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <form method="GET" action="{{ route('equipo.reportes.index') }}" style="display:flex; gap:8px; align-items:center;">
        <input
          name="q"
          value="{{ $q ?? '' }}"
          placeholder="Buscar por título, RUC o razón social..."
        />
        <button type="submit">Buscar</button>

        @if(!empty($q))
          <a href="{{ route('equipo.reportes.index') }}" style="font-size:13px;">
            Limpiar
          </a>
        @endif
      </form>

      <a href="{{ route('equipo.reportes.create') }}">
        Nuevo reporte
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div style="padding:10px; margin:10px 0; border:1px solid #ddd; border-radius:10px;">
      {{ session('ok') }}
    </div>
  @endif

  @if(session('error'))
    <div style="padding:10px; margin:10px 0; border:1px solid #f3b4b4; background:#fff5f5; border-radius:10px;">
      {{ session('error') }}
    </div>
  @endif

  <div class="table-wrap">
    <table class="table">
      <thead>
        <tr>
          <th>Empresa</th>
          <th>RUC</th>
          <th>Título</th>
          <th>Periodo</th>
          <th>Estado</th>
          <th style="width:220px;">Acciones</th>
        </tr>
      </thead>

      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->company?->razon_social ?? '—' }}</td>
            <td>{{ $r->company?->ruc ?? '—' }}</td>
            <td>{{ $r->titulo }}</td>
            <td>
              @php
                $pm = $r->periodo_mes
                  ? str_pad((string)$r->periodo_mes, 2, '0', STR_PAD_LEFT)
                  : '—';
                $py = $r->periodo_anio ?? '—';
              @endphp
              {{ $pm }} / {{ $py }}
            </td>
            <td>{{ $r->estado }}</td>

            {{-- ✅ AQUÍ VA EL SHOW --}}
            <td style="display:flex; gap:10px; align-items:center;">
              <a href="{{ route('equipo.reportes.show', $r) }}">
                Ver
              </a>

              <a href="{{ route('equipo.reportes.edit', $r) }}">
                Editar
              </a>

              <form
                method="POST"
                action="{{ route('equipo.reportes.destroy', $r) }}"
                style="display:inline;"
              >
                @csrf
                @method('DELETE')
                <button
                  type="submit"
                  onclick="return confirm('¿Eliminar reporte?')"
                >
                  Eliminar
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="padding:18px;">
              Aún no hay reportes.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pager">
    {{ $rows->links() }}
  </div>
</div>
@endsection
