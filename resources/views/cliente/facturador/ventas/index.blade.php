@extends('layouts.app-cliente')

@section('title', 'Historial de Ventas')

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

/* Status Badges */
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
.status-pending { background: #fef9c3; color: #854d0e; }
.status-void { background: #fee2e2; color: #991b1b; }

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #64748b;
    padding: 4px;
    border-radius: 4px;
}
.action-btn:hover { background: #f1f5f9; color: #0f172a; }

</style>
@endpush

@section('content')
<div class="index-container">
    
    <!-- Encabezado -->
    <div class="index-header">
        <h1 class="index-title">
            <i data-lucide="shopping-cart"></i> Historial de Ventas
        </h1>
        <a href="{{ route('cliente.facturador.ventas.create') }}" class="btn-primary">
            <i data-lucide="plus"></i> Nueva Venta
        </a>
    </div>

    <!-- TABS DE NAVEGACIÓN -->
    <div style="display: flex; gap: 0; border-bottom: 1px solid #cbd5e1; margin-bottom: 20px;">
        <a href="{{ route('cliente.facturador.ventas.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #0f172a; border-bottom: 2px solid #0f172a; text-decoration: none; font-size: 14px;">
           Ventas
        </a>
        <a href="{{ route('cliente.facturador.facturacion.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #64748b; text-decoration: none; font-size: 14px; border-bottom: 2px solid transparent;">
           Facturación
        </a>
        <a href="{{ route('cliente.facturador.cotizaciones.index') }}" 
           style="padding: 10px 20px; font-weight: 600; color: #64748b; text-decoration: none; font-size: 14px; border-bottom: 2px solid transparent;">
           Cotizaciones
        </a>
    </div>

    <!-- Barra de Filtros -->
    <div class="filter-bar">
        <div class="search-input-wrapper" style="flex: 1; min-width: 250px; position: relative;">
            <i data-lucide="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8;"></i>
            <input type="text" class="filter-input" placeholder="Buscar por cliente, RUC o documento..." style="width: 100%; padding-left: 32px;">
        </div>

        <div class="filter-group">
            <label style="font-size: 12px; font-weight: 600; color: #64748b;">Desde:</label>
            <input type="date" class="filter-input" value="{{ date('Y-m-01') }}">
        </div>

        <div class="filter-group">
            <label style="font-size: 12px; font-weight: 600; color: #64748b;">Hasta:</label>
            <input type="date" class="filter-input" value="{{ date('Y-m-d') }}">
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
                    <th>ID</th>
                    <th>Fecha Emisión</th>
                    <th>Cliente</th>
                    <th>Documento</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                {{-- Mock Data Row 1 --}}
                <tr>
                    <td style="font-weight: 600; color: #64748b;">#1024</td>
                    <td>12/02/2026 10:45</td>
                    <td>
                        <div style="font-weight: 600; color: #0f172a;">JUAN PEREZ</div>
                        <div style="font-size: 11px; color: #94a3b8;">10456789012</div>
                    </td>
                    <td><span style="font-family: monospace; font-weight: 600;">B001-00000345</span></td>
                    <td style="text-align: right; font-weight: 700;">S/ 150.00</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-ok">
                            <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> EMITIDO
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Ver Detalles"><i data-lucide="eye" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Imprimir"><i data-lucide="printer" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Más opciones"><i data-lucide="more-horizontal" style="width: 16px;"></i></button>
                    </td>
                </tr>

                 {{-- Mock Data Row 2 --}}
                 <tr>
                    <td style="font-weight: 600; color: #64748b;">#1023</td>
                    <td>12/02/2026 09:30</td>
                    <td>
                        <div style="font-weight: 600; color: #0f172a;">EMPRESA DE TRANSPORTES SAC</div>
                        <div style="font-size: 11px; color: #94a3b8;">20123456789</div>
                    </td>
                    <td><span style="font-family: monospace; font-weight: 600;">F001-00000128</span></td>
                    <td style="text-align: right; font-weight: 700;">S/ 2,450.00</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-ok">
                            <i data-lucide="check-circle" style="width: 12px; height: 12px;"></i> EMITIDO
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Ver Detalles"><i data-lucide="eye" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Imprimir"><i data-lucide="printer" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Más opciones"><i data-lucide="more-horizontal" style="width: 16px;"></i></button>
                    </td>
                </tr>

                {{-- Mock Data Row 3 --}}
                <tr>
                    <td style="font-weight: 600; color: #64748b;">#1022</td>
                    <td>11/02/2026 18:15</td>
                    <td>
                        <div style="font-weight: 600; color: #0f172a;">CLIENTE VARIOS</div>
                        <div style="font-size: 11px; color: #94a3b8;">00000000</div>
                    </td>
                    <td><span style="font-family: monospace; font-weight: 600;">B001-00000344</span></td>
                    <td style="text-align: right; font-weight: 700;">S/ 25.50</td>
                    <td style="text-align: center;">
                        <span class="status-badge status-void">
                            <i data-lucide="x-circle" style="width: 12px; height: 12px;"></i> ANULADO
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <button class="action-btn" title="Ver Detalles"><i data-lucide="eye" style="width: 16px;"></i></button>
                        <button class="action-btn" title="Más opciones"><i data-lucide="more-horizontal" style="width: 16px;"></i></button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
{{-- ✅ Lucide (ponlo 1 vez en tu layout principal si puedes) --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  // Si ya lo tienes en el layout principal, puedes borrar este bloque.
  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
  });
</script>
@endpush