@extends('layouts.app-equipo')

@section('title', 'Nueva Factura Gravada')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/facturas.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="card feasy">
  <h2>Nueva Factura (01) Gravada - Feasy</h2>
  <div class="muted">
    Autocompleta cliente con AQPF (RUC) y emite a Feasy. Luego puedes consultar y descargar XML/CDR/PDF.
  </div>

  <form id="frm">
    <h3 style="margin-top:18px;">Documento</h3>
    <div class="row">
      <div class="col-3">
        <label>Serie</label>
        <input name="serie" value="F001" required maxlength="4">
      </div>
      <div class="col-3">
        <label>Número</label>
        <input name="numero" value="1" required>
      </div>
      <div class="col-3">
        <label>Fecha emisión</label>
        <input type="date" name="fecha_emision" required value="{{ date('Y-m-d') }}">
      </div>
      <div class="col-3">
        <label>Hora emisión</label>
        <input type="time" name="hora_emision" required value="{{ date('H:i') }}">
      </div>
    </div>

    {{-- (tu bloque forma de pago / detracción lo dejo tal cual) --}}
    <div class="row">
      <div class="col-3">
        <label>Forma de pago</label>
        <select name="forma_pago" id="forma_pago" required>
          <option value="contado">Contado</option>
          <option value="credito">Crédito</option>
        </select>
      </div>

      <div class="col-3" id="wrap_venc" style="display:none;">
        <label>Fecha vencimiento</label>
        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento">
      </div>

      <div class="col-3" id="wrap_cuota" style="display:none;">
        <label>Monto cuota</label>
        <input type="number" step="0.01" name="monto_cuota" id="monto_cuota">
      </div>
    </div>

    <h3 style="margin-top:18px;">Detracción (SPOT)</h3>
    <div class="row">
      <div class="col-3">
        <label>¿Sujeta a detracción?</label>
        <select name="detraccion_activa" id="detraccion_activa" required>
          <option value="0">No</option>
          <option value="1">Sí</option>
        </select>
      </div>

      <div class="col-3 detra" style="display:none;">
        <label>% detracción</label>
        <input type="number" step="0.01" name="detraccion_porcentaje" id="detraccion_porcentaje" value="12.00">
      </div>

      <div class="col-3 detra" style="display:none;">
        <label>Bien/Servicio (SUNAT)</label>
        <input name="detraccion_bien_servicio" id="detraccion_bien_servicio" value="022">
      </div>

      <div class="col-3 detra" style="display:none;">
        <label>Medio de pago</label>
        <input name="detraccion_medio_pago" id="detraccion_medio_pago" value="003">
      </div>

      <div class="col-4 detra" style="display:none;">
        <label>Cuenta BN</label>
        <input name="detraccion_cta_bn" id="detraccion_cta_bn" placeholder="00091117703">
      </div>
    </div>

    <h3 style="margin-top:18px;">Cliente</h3>
    <div class="row">
      <div class="col-3">
        <label>Tipo Doc</label>
        {{-- ✅ Solo RUC porque el backend valida in:6 --}}
        <select name="cliente_tipo_doc" id="cliente_tipo_doc" required>
          <option value="6">RUC (6)</option>
        </select>
      </div>

      <div class="col-4">
        <label>Número Doc</label>
        <input name="cliente_numero_doc" id="cliente_numero_doc" required placeholder="205xxxxxxx (11 dígitos)">
      </div>

      <div class="col-2" style="display:flex;align-items:end;">
        <button type="button" class="btn btn-warn" id="btnLookup">Buscar</button>
      </div>

      <div class="col-12">
        <div class="muted" id="lookupMsg"></div>
      </div>

      <div class="col-6">
        <label>Razón social / Nombre</label>
        <input name="cliente_nombre" id="cliente_nombre" required placeholder="CLIENTE SAC">
      </div>
      <div class="col-6">
        <label>Correo</label>
        <input name="cliente_correo" id="cliente_correo" type="email" placeholder="cliente@mail.com">
      </div>
      <div class="col-12">
        <label>Dirección</label>
        <input name="cliente_direccion" id="cliente_direccion" placeholder="Calle ...">
      </div>
    </div>

    <h3 style="margin-top:18px;">Ítems</h3>
    <div class="row">
      <div class="col-2">
        <label>Tipo</label>
        <select id="it_tipo">
          <option value="P">Producto (P)</option>
          <option value="S">Servicio (S)</option>
        </select>
      </div>
      <div class="col-3">
        <label>Código interno</label>
        <input id="it_codigo" placeholder="P01">
      </div>
      <div class="col-2">
        <label>Unidad</label>
        <select id="it_unidad">
          <option value="NIU">NIU</option>
          <option value="ZZ">ZZ</option>
        </select>
      </div>
      <div class="col-5">
        <label>Descripción</label>
        <input id="it_desc" placeholder="PRODUCTO / SERVICIO">
      </div>
      <div class="col-2">
        <label>Cantidad</label>
        <input id="it_cant" type="number" step="0.01" value="1">
      </div>
      <div class="col-2">
        <label>Precio Unit (con IGV)</label>
        <input id="it_precio" type="number" step="0.01" value="118">
      </div>
      <div class="col-12">
        <button type="button" class="btn btn-ghost" id="btnAdd">+ Agregar ítem</button>
      </div>
    </div>

    <table id="tbl">
      <thead>
        <tr>
          <th>#</th><th>Tipo</th><th>Código</th><th>Unidad</th><th>Descripción</th>
          <th>Cant</th><th>Precio Unit</th><th>Total</th><th></th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div class="totals">
      <div class="pill">Gravado: <b id="t_grav">0.00</b></div>
      <div class="pill">IGV: <b id="t_igv">0.00</b></div>
      <div class="pill">Total: <b id="t_tot">0.00</b></div>
    </div>

    <div class="actions">
      <button type="button" class="btn btn-ghost" id="btnPreview">Ver Request</button>
      <button type="submit" class="btn btn-primary">Emitir (Feasy)</button>
    </div>
  </form>

  {{-- ✅ Panel de consulta / descargas --}}
  <div class="card" style="margin-top:14px;">
    <div class="muted">Consulta / Descargas</div>
    <div class="row" style="margin-top:10px;">
      <div class="col-3">
        <button type="button" class="btn btn-ghost" id="btnConsultar">Consultar (FEASY)</button>
      </div>
      <div class="col-9" style="display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn btn-ghost" id="btnXml" href="#" style="display:none;" target="_blank">Descargar XML</a>
        <a class="btn btn-ghost" id="btnCdr" href="#" style="display:none;" target="_blank">Descargar CDR</a>
        <a class="btn btn-ghost" id="btnPdf" href="#" style="display:none;" target="_blank">Descargar PDF</a>
      </div>
      <div class="col-12">
        <div class="muted" id="consultaMsg"></div>
      </div>
    </div>
  </div>

</div>

<pre id="out"></pre>
@endsection

@push('scripts')
<script>
  window.FEASY = {
    storeUrl: "{{ route('equipo.facturas.store') }}",
    consultarUrl: "{{ route('equipo.facturas.consultar') }}",
    // endpoint de descargas: /equipo/facturas/descargar?url=...
    descargarUrl: "{{ route('equipo.facturas.descargar') }}",
    lookupRucUrl: "{{ url('/equipo/lookup/ruc') }}",
    csrf: "{{ csrf_token() }}"
  };
</script>
<script src="{{ asset('js/facturas.js') }}"></script>
@endpush
