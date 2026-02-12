@extends('layouts.app-cliente')

@section('title', 'Inventario de Paquetes')

@push('styles')
<style>
/* =========================================
   DISEÑO PROFESIONAL / ERP (LISTADO)
   ========================================= */

.index-container {
    padding: 20px;
    background-color: #f1f5f9; /* Slate-100 */
    min-height: calc(100vh - 60px);
}

/* Header & Filters */
.index-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.index-title {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-bar {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-input {
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 13px;
    color: #334155;
    outline: none;
    min-width: 150px;
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

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
}

.status-ok { background: #dcfce7; color: #166534; }

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    padding: 4px;
    border-radius: 4px;
}
.action-btn:hover { background: #f1f5f9; color: #0f172a; }

/* MODAL STYLES (Preserved) */
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
.fact-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.fact-modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
.fact-btn-ghost { background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }
.fact-btn-ghost:hover { background: #f8fafc; }

/* Add Row inside Modal */
.fact-addrow { display: flex; gap: 8px; margin-bottom: 8px; }
.fact-plus {
    background: #0f172a; color: #fff; border: none; width: 40px; border-radius: 6px;
    font-size: 18px; cursor: pointer; display: grid; place-items: center;
}
.fact-empty-pack { font-size: 12px; color: #94a3b8; font-style: italic; text-align: center; padding: 10px; border: 1px dashed #cbd5e1; border-radius: 6px; }
.fact-pill { background: #eff6ff; color: #1e3a8a; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; border: 1px solid #dbeafe; }
</style>
@endpush

@section('content')
<div class="index-container">
    
    <!-- Encabezado -->
    <div class="index-header">
        <h1 class="index-title">
            <i data-lucide="layers"></i> Paquetes / Kits
        </h1>
        <button class="btn-primary" type="button" data-open="modalPaquete">
            <i data-lucide="plus"></i> Nuevo Paquete
        </button>
    </div>

    <!-- TABS DE NAVEGACIÓN -->
    <div style="display: flex; gap: 0; border-bottom: 1px solid #cbd5e1; margin-bottom: 20px;">
        <a href="{{ route('cliente.facturador.productos.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #64748b; border-bottom: 2px solid transparent; text-decoration: none; font-size: 14px;">
           Productos
        </a>
        <a href="{{ route('cliente.facturador.paquetes.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #0f172a; text-decoration: none; font-size: 14px; border-bottom: 2px solid #0f172a;">
           Paquetes
        </a>
        <a href="{{ route('cliente.facturador.categorias.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #64748b; text-decoration: none; font-size: 14px; border-bottom: 2px solid transparent;">
           Categorías
        </a>
    </div>

    <!-- Barra de Filtros -->
    <div class="filter-bar">
        <div class="search-input-wrapper" style="flex: 1; min-width: 250px; position: relative;">
            <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8;"></i>
            <input type="text" class="filter-input" placeholder="Buscar paquete..." style="width: 100%; padding-left: 32px;">
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
                    <th>Código</th>
                    <th>Nombre / Descripción</th>
                    <th>Unidad</th>
                    <th style="text-align: right;">Precio</th>
                    <th style="text-align: center;">Elementos</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- Mock Data Row 1 --}}
                <tr>
                    <td style="font-weight: 600; color: #64748b;">KIT-001</td>
                    <td><div style="font-weight: 600; color: #0f172a;">KIT ESCOLAR BÁSICO</div></td>
                    <td><span style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">PAQUETE</span></td>
                    <td style="text-align: right; font-weight: 700;">S/ 45.00</td>
                    <td style="text-align: center;">
                        <span class="fact-pill">3 PRODUCTOS</span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Editar"><i data-lucide="edit-3" style="width: 16px;"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL PAQUETE (PRESERVED LOGIC) --}}
<div class="fact-modal" id="modalPaquete" aria-hidden="true">
  <div class="fact-backdrop" data-close="1"></div>
  <div class="fact-modal-card" role="dialog" aria-modal="true">
    <button class="fact-x" data-close="1" type="button">✕</button>
    <h2 class="fact-modal-title">Nuevo paquete</h2>

    <div class="fact-form">
      <div class="fact-field">
        <label>Nombre <span class="fact-req">*</span></label>
        <input class="fact-input" type="text" placeholder="Ej. Kit de Instalación">
      </div>

      <div class="fact-field">
        <label>Código <span class="fact-req">*</span></label>
        <input class="fact-input" type="text" placeholder="Ej. KIT-001">
      </div>

      <div class="fact-field">
        <label>Precio de venta <span class="fact-req">*</span></label>
        <div class="fact-grid-2">
          <select class="fact-input"><option>S/</option><option>$</option></select>
          <input class="fact-input" type="number" step="0.01" placeholder="0.00">
        </div>
      </div>

      <div class="fact-field">
        <div class="fact-grid-2">
          <select class="fact-input"><option>(NIU) UNIDAD</option><option>(PK) PAQUETE</option></select>
          <select class="fact-input"><option>IGV (18.00%)</option></select>
        </div>
      </div>

      <div class="fact-field">
        <label>Productos del paquete</label>
        <div class="fact-addrow">
          <input class="fact-input" placeholder="Buscar producto...">
          <input class="fact-input" type="number" placeholder="Cant." style="max-width:80px;">
          <button class="fact-plus" type="button">+</button>
        </div>
        <div class="fact-empty-pack">No hay productos en el paquete</div>
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

  // ================== MODALES (open/close) ==================
  const openModal = (id) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('show');
    el.setAttribute('aria-hidden', 'false');
    const focusable = el.querySelector('input,select,button');
    if (focusable) focusable.focus();
  };

  const closeModal = (modalEl) => {
    if (!modalEl) return;
    modalEl.classList.remove('show');
    modalEl.setAttribute('aria-hidden', 'true');
  };

  document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open]');
    if (openBtn) {
      e.preventDefault();
      openModal(openBtn.dataset.open);
      return;
    }
    const closeBtn = e.target.closest('[data-close]');
    if (closeBtn) {
      e.preventDefault();
      const modal = closeBtn.closest('.fact-modal');
      closeModal(modal);
    }
  });

  document.querySelectorAll('.fact-modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
      if (e.target.classList.contains('fact-modal')) closeModal(modal); // Backdrop click
    });
  });
});
</script>
@endpush