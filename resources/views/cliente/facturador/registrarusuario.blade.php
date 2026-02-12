@extends('layouts.app-cliente')

@section('title', 'Clientes y Proveedores')

@push('styles')
<style>
/* =========================================
   DISEÑO PROFESIONAL / ERP (LISTADO)
   ========================================= */

.index-container {
    padding: 24px;
    background-color: #f1f5f9; /* Slate-100 */
    min-height: calc(100vh - 60px);
    font-family: 'Inter', system-ui, sans-serif;
}

/* Header & Filters */
.index-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.index-title {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 12px;
}

.filter-bar {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-input {
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
    color: #334155;
    outline: none;
    min-width: 200px;
}

.filter-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.btn-primary {
    background: #0f172a;
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-primary:hover {
    background: #1e293b;
}

.btn-secondary {
    background: #fff;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-secondary:hover {
    background: #f8fafc;
    color: #0f172a;
}

/* Tabla */
.table-card {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.erp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.erp-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.erp-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
}

.erp-table tr:last-child td {
    border-bottom: none;
}

.erp-table tr:hover {
    background-color: #f8fafc;
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
}
.badge-client { background: #dbf4ff; color: #075985; border: 1px solid #bae6fd; }
.badge-provider { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    padding: 4px;
    border-radius: 4px;
}
.action-btn:hover { background: #f1f5f9; color: #0f172a; }


/* MODAL STYLES (Preserved Logic) */
.fact-modal {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    z-index: 999; display: none; place-items: center;
}
.fact-modal.show { display: grid; }
.fact-backdrop {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15,23,42,0.6); backdrop-filter: blur(2px);
}
.fact-modal-card {
    position: relative; background: #fff; width: 90%; max-width: 600px;
    border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    padding: 24px; animation: modalFadeIn 0.2s ease-out; z-index: 1000;
    max-height: 90vh; overflow-y: auto;
}
@keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.fact-x {
    position: absolute; top: 16px; right: 16px; background: none; border: none;
    font-size: 18px; color: #94a3b8; cursor: pointer;
}
.fact-x:hover { color: #0f172a; }
.fact-modal-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
.fact-field { margin-bottom: 16px; }
.fact-field label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.fact-req { color: #ef4444; }
.fact-input {
    width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;
    font-size: 14px; color: #0f172a; outline: none;
}
.fact-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.1); }
.fact-modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
.fact-btn-ghost { background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }
.fact-btn-ghost:hover { background: #f8fafc; }
.fact-seg-row { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }
.fact-seg {
    background: #f1f5f9; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 16px;
    border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px;
}
.fact-seg.active { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.fact-doc-input { display: flex; gap: 8px; }
.fact-btn-blue { background: #3b82f6; color: #fff; border: none; padding: 0 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }
.fact-btn-blue:hover { background: #2563eb; }

</style>
@endpush

@section('content')
<div class="index-container">
    
    <!-- Encabezado -->
    <div class="index-header">
        <h1 class="index-title">
            <i data-lucide="users"></i> Clientes / Proveedores
        </h1>
        <button class="btn-primary" type="button" id="btnOpenModal">
            <i data-lucide="user-plus"></i> Nuevo Cliente / Prov.
        </button>
    </div>

    <!-- Barra de Filtros -->
    <div class="filter-bar">
        <div class="search-input-wrapper" style="flex: 1; min-width: 250px; position: relative;">
            <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8;"></i>
            <input type="text" class="filter-input" placeholder="Buscar por Nombre, RUC, DNI..." style="width: 100%; padding-left: 32px;">
        </div>

        <div class="filter-group">
            <select class="filter-input">
                <option value="">Todos los tipos</option>
                <option value="Cliente">Solo Clientes</option>
                <option value="Proveedor">Solo Proveedores</option>
            </select>
        </div>

        <button class="btn-secondary">
            <i data-lucide="filter"></i> Filtrar
        </button>
    </div>

    <!-- Tabla de Datos -->
    <div class="table-card">
        <table class="erp-table">
            <thead>
                <tr>
                    <th>Nombre / Razón Social</th>
                    <th>Documento (RUC/DNI)</th>
                    <th>Tipo</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                 {{-- Mock Data --}}
                  @php
                    $rows = [
                      ['name'=>'LETS DECO S.A.C.', 'doc'=>'RUC 20611811358', 'type'=>'Cliente', 'addr'=>'CAL. ATAHUALPA NRO. 558 MIRAFLORES', 'phone'=>'-'],
                      ['name'=>'JUAN PEREZ', 'doc'=>'DNI 45892133', 'type'=>'Cliente', 'addr'=>'-', 'phone'=>'982171328'],
                      ['name'=>'DISTRIBUIDORA ELÉCTRICA SAC', 'doc'=>'RUC 20551234567', 'type'=>'Proveedor', 'addr'=>'AV. ARGENTINA 123', 'phone'=>'01-456-7890'],
                      ['name'=>'ASENCIOS JIMENEZ MARCOS CESAR', 'doc'=>'RUC 10321072416', 'type'=>'Cliente', 'addr'=>'JR. LIMA 456', 'phone'=>'+51996593408'],
                    ];
                  @endphp

                  @foreach($rows as $r)
                    <tr>
                        <td style="font-weight: 600; color: #0f172a;">{{ $r['name'] }}</td>
                        <td style="color: #475569; font-family: monospace;">{{ $r['doc'] }}</td>
                        <td>
                             <span class="badge {{ $r['type'] === 'Cliente' ? 'badge-client' : 'badge-provider' }}">
                                {{ $r['type'] }}
                            </span>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">{{ $r['addr'] }}</td>
                        <td style="font-size: 12px; color: #64748b;">{{ $r['phone'] }}</td>
                        <td style="text-align: center;">
                            <button class="action-btn" title="Editar"><i data-lucide="edit-3" style="width: 16px;"></i></button>
                            <button class="action-btn" title="Más opciones"><i data-lucide="more-horizontal" style="width: 16px;"></i></button>
                        </td>
                    </tr>
                  @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL (Preserved Logic) --}}
<div class="fact-modal" id="modal" aria-hidden="true">
  <div class="fact-backdrop" data-close="1"></div>
  <div class="fact-modal-card" role="dialog" aria-modal="true">
    <button class="fact-x" data-close="1" type="button">✕</button>
    <h2 class="fact-modal-title">Nuevo cliente / proveedor</h2>

    <div class="fact-seg-row">
      <button class="fact-seg active" type="button">RUC <i data-lucide="check" style="width:12px;"></i></button>
      <button class="fact-seg" type="button">DNI</button>
      <div style="margin-left: auto;">
        <select class="fact-input" style="padding: 6px;">
          <option>OTROS DOCUMENTOS</option>
          <option>CARNET EXTRANJERIA</option>
          <option>PASAPORTE</option>
        </select>
      </div>
    </div>

    <div class="fact-form">
      <div class="fact-field">
        <label>N° de Documento <span class="fact-req">*</span></label>
        <div class="fact-doc-input">
          <input class="fact-input" type="text" placeholder="Ingrese número..." />
          <button class="fact-btn-blue" type="button">SUNAT / RENIEC</button>
        </div>
      </div>

      <div class="fact-field">
        <label>Nombre legal <span class="fact-req">*</span></label>
        <input class="fact-input" type="text" placeholder="Razón Social o Nombre Completo"/>
      </div>

      <div class="fact-field">
        <label>Dirección</label>
        <input class="fact-input" type="text" placeholder="Dirección fiscal" />
      </div>

      <div class="fact-field">
        <label>Tipo</label>
        <select class="fact-input">
          <option value="Cliente">Cliente</option>
          <option value="Proveedor">Proveedor</option>
        </select>
      </div>

      <div class="fact-modal-actions">
        <button class="fact-btn-ghost" type="button" data-close="1">Cancelar</button>
        <button class="btn-primary" type="button">Guardar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- ✅ Lucide --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.lucide) lucide.createIcons();

  // Modal Logic
  const modal = document.getElementById('modal');
  const openBtn = document.getElementById('btnOpenModal');

  if (modal && openBtn) {
      const openModal = () => {
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        if(window.lucide) lucide.createIcons();
      };
      const closeModal = () => {
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
      };
      openBtn.addEventListener('click', openModal);
      modal.addEventListener('click', (e) => {
        if (e.target?.dataset?.close) closeModal();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
      });
  }

  // Seg Logic (Tabs)
  document.querySelectorAll('.fact-seg').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.fact-seg').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });
});
</script>
@endpush