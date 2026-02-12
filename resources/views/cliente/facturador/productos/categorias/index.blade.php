@extends('layouts.app-cliente')

@section('title', 'Categorías de Productos')

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

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    padding: 4px;
    border-radius: 4px;
}
.action-btn:hover { background: #f1f5f9; color: #0f172a; }

/* MODAL STYLES (Required for partial) */
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
.fact-btn-primary { background: #0f172a; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="index-container">
    
    <!-- Encabezado -->
    <div class="index-header">
        <h1 class="index-title">
            <i data-lucide="tag"></i> Categorías
        </h1>
        <button class="btn-primary" type="button" data-open="modalCategoria">
            <i data-lucide="plus"></i> Nueva Categoría
        </button>
    </div>

    <!-- TABS DE NAVEGACIÓN -->
    <div style="display: flex; gap: 0; border-bottom: 1px solid #cbd5e1; margin-bottom: 20px;">
        <a href="{{ route('cliente.facturador.productos.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #64748b; border-bottom: 2px solid transparent; text-decoration: none; font-size: 14px;">
           Productos
        </a>
        <a href="{{ route('cliente.facturador.paquetes.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #64748b; text-decoration: none; font-size: 14px; border-bottom: 2px solid transparent;">
           Paquetes
        </a>
        <a href="{{ route('cliente.facturador.categorias.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #0f172a; text-decoration: none; font-size: 14px; border-bottom: 2px solid #0f172a;">
           Categorías
        </a>
    </div>

    <!-- Tabla de Datos -->
    <div class="table-card">
        <table class="erp-table">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Orden</th>
                    <th>Nombre de Categoría</th>
                    <th style="text-align: center;">Por Defecto</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- Mock Data Row 1 --}}
                <tr>
                    <td style="text-align: center; color: #64748b;">1</td>
                    <td><div style="font-weight: 600; color: #0f172a;">SERVICIOS</div></td>
                    <td style="text-align: center;">
                        <span style="color: #22c55e;"><i data-lucide="check" style="width: 16px;"></i></span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Editar"><i data-lucide="edit-3" style="width: 16px;"></i></button>
                    </td>
                </tr>
                 {{-- Mock Data Row 2 --}}
                 <tr>
                    <td style="text-align: center; color: #64748b;">2</td>
                    <td><div style="font-weight: 600; color: #0f172a;">MATERIALES ELÉCTRICOS</div></td>
                    <td style="text-align: center;">
                        <span style="color: #cbd5e1;">-</span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Editar"><i data-lucide="edit-3" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Eliminar"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                    </td>
                </tr>
                 {{-- Mock Data Row 3 --}}
                 <tr>
                    <td style="text-align: center; color: #64748b;">3</td>
                    <td><div style="font-weight: 600; color: #0f172a;">ILUMINACIÓN</div></td>
                    <td style="text-align: center;">
                        <span style="color: #cbd5e1;">-</span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Editar"><i data-lucide="edit-3" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Eliminar"><i data-lucide="trash-2" style="width: 16px;"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@include('cliente.facturador.productos.categorias._modal-nueva-categoria')

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