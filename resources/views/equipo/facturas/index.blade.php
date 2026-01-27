@extends('layouts.app-equipo')

@section('title', 'Facturas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ListadoFacturas.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
    <div>
      <h2 style="margin:0;">Facturas</h2>
      <div class="muted">Historial de emisiones FEASY/SUNAT</div>
    </div>
    <a class="btn btn-primary" href="{{ route('equipo.facturas.create') }}">+ Nueva</a>
  </div>

  <div style="overflow:auto;margin-top:14px;">
    <table class="table" style="width:100%;">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Serie</th>
          <th>Número</th>
          <th>Cliente</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Código</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r->created_at?->format('Y-m-d H:i') }}</td>
            <td>{{ $r->serie }}</td>
            <td>{{ $r->numero }}</td>
            <td style="max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              {{ $r->cliente_numero_doc }} - {{ $r->cliente_nombre }}
            </td>
            <td>{{ number_format((float)$r->monto_total, 2) }}</td>

            <td>
              @if($r->success && $r->codigo_respuesta === '0')
                Aceptada
              @elseif($r->success)
                Enviada
              @else
                Error
              @endif
            </td>

            <td>{{ $r->codigo_respuesta ?? '—' }}</td>

            <td style="white-space:nowrap;">
              {{-- 1) Botón para consultar y llenar rutas --}}
              <button class="btn btn-ghost js-refresh"
                      data-id="{{ $r->id }}">
                Actualizar
              </button>

              {{-- 2) Descargas (solo si existe ruta) --}}
              @if(!empty($r->ruta_xml))
                <a class="btn btn-ghost"
                   href="{{ route('equipo.facturas.descargar', ['url' => $r->ruta_xml]) }}"
                   target="_blank">XML</a>
              @endif

              @if(!empty($r->ruta_cdr))
                <a class="btn btn-ghost"
                   href="{{ route('equipo.facturas.descargar', ['url' => $r->ruta_cdr]) }}"
                   target="_blank">CDR</a>
              @endif

              @if(!empty($r->ruta_pdf))
                <a class="btn btn-ghost"
                   href="{{ route('equipo.facturas.descargar', ['url' => $r->ruta_pdf]) }}"
                   target="_blank">PDF</a>
              @endif

              {{-- Hint si aún no hay rutas --}}
              @if(empty($r->ruta_xml) && empty($r->ruta_cdr) && empty($r->ruta_pdf))
                <span class="muted" style="margin-left:8px;">(sin rutas)</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8">Aún no hay facturas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:12px;">
    {{ $rows->links() }}
  </div>
</div>

@push('scripts')
<script>
  document.querySelectorAll('.js-refresh').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      btn.disabled = true;
      const old = btn.textContent;
      btn.textContent = '...';

      try {
        const res = await fetch(`{{ url('/equipo/facturas') }}/${id}/refresh`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          }
        });

        const data = await res.json();
        // recarga para que aparezcan XML/CDR/PDF
        location.reload();
      } catch (e) {
        alert('Error consultando FEASY');
        btn.disabled = false;
        btn.textContent = old;
      }
    });
  });
</script>
@endpush
@endsection
