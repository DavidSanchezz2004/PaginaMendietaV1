@extends('layouts.app-cliente')

@section('title', 'Nueva Venta')

@push('styles')
<style>
/* =========================================
   DISEÑO PROFESIONAL / ERP (NO "IA-LIKE")
   ========================================= */

/* Reset básico para inputs */
input, select, textarea {
    font-family: inherit;
    font-size: 14px;
}

/* Layout Principal */
.sales-container {
    display: grid;
    grid-template-columns: 1fr 380px; /* Panel izquierdo flexible, derecho fijo */
    gap: 20px;
    padding: 20px;
    height: calc(100vh - 60px); /* Ajuste según altura del header */
    overflow: hidden;
    background-color: #f1f5f9; /* Slate-100 */
}

@media (max-width: 1024px) {
    .sales-container {
        grid-template-columns: 1fr;
        height: auto;
        overflow-y: auto;
    }
}

/* Paneles (Cards) */
.sales-card {
    background: #fff;
    border: 1px solid #cbd5e1; /* Slate-300 */
    border-radius: 6px; /* Bordes sutiles, no muy redondos */
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
}

/* Header de Facturación */
.sales-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sales-header h2 {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b; /* Slate-800 */
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Buscador de Productos */
.product-search-bar {
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
    background: #fff;
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input-wrapper {
    flex: 1;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    outline: none;
    transition: all 0.2s;
}

.search-input:focus {
    border-color: #3b82f6; /* Blue-500 */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

/* Tabla de Items */
.items-table-container {
    flex: 1;
    overflow-y: auto;
    background: #fff;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.items-table th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.items-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
}

.items-table tr:hover {
    background-color: #f8fafc;
}

.col-qty input {
    width: 60px;
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    text-align: center;
}

.btn-remove {
    color: #ef4444; /* Red-500 */
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
}

.btn-remove:hover {
    background: #fee2e2;
}

/* Panel Derecho (Totales y Acciones) */
.summary-section {
    padding: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.summary-block {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
}

.summary-block:last-child {
    border-bottom: none;
}

/* Switch Tipo Documento */
.doc-type-switch {
    display: flex;
    background: #e2e8f0;
    border-radius: 6px;
    padding: 4px;
    margin-bottom: 16px;
}

.doc-type-option {
    flex: 1;
    text-align: center;
    padding: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 4px;
    color: #64748b;
    transition: all 0.2s;
}

.doc-type-option.active {
    background: #fff;
    color: #0f172a;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Cliente */
.client-info {
    margin-top: 10px;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: #f8fafc;
}

.client-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 4px;
}
.client-label { color: #64748b; font-weight: 500; }
.client-value { color: #0f172a; font-weight: 600; }

/* Totales */
.totals-grid {
    display: grid;
    gap: 8px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #475569;
}

.total-row.grand-total {
    font-size: 20px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 8px;
    padding-top: 12px;
    border-top: 2px dashed #e2e8f0;
}

/* Botón Procesar */
.action-area {
    margin-top: auto; /* Empuja al fondo */
    padding: 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.btn-process {
    width: 100%;
    padding: 14px;
    background: #0f172a; /* Slate-900 */
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}

.btn-process:hover {
    background: #1e293b;
}

.btn-process:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}

/* Helper Text */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
    font-style: italic;
}

</style>
@endpush

@section('content')
<div class="sales-container">
    
    <!-- LEFT PANEL: Productos y Listado -->
    <div class="sales-card">
        <div class="sales-header">
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('cliente.facturador.ventas.index') }}" style="text-decoration: none; color: #64748b; font-size: 20px; line-height: 1;">&larr;</a>
                <h2>
                    <span style="font-size: 18px;">🛒</span> Nueva Venta
                </h2>
            </div>
            <div style="font-size: 13px; color: #64748b;">
                {{ date('d/m/Y') }} <span id="clock" style="margin-left: 5px;">{{ date('H:i') }}</span>
            </div>
        </div>

        <!-- Buscador -->
        <div class="product-search-bar">
            <div class="search-input-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" id="productSearch" class="search-input" placeholder="Buscar producto (Código o Nombre)..." autocomplete="off" autofocus>
            </div>
            <button class="btn-process" style="width: auto; padding: 10px 16px; font-size: 14px;" onclick="openProductModal()">
                + Catálogo
            </button>
        </div>

        <!-- Tabla -->
        <div class="items-table-container">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Descripción</th>
                        <th style="width: 80px; text-align: center;">Unid.</th>
                        <th style="width: 100px; text-align: right;">Precio</th>
                        <th style="width: 100px; text-align: center;">Cant.</th>
                        <th style="width: 100px; text-align: right;">Total</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="cartItems">
                    <!-- Filas generadas por JS -->
                    <tr id="emptyRow">
                        <td colspan="7" class="empty-state">
                            No hay productos en la lista. <br> Usa el buscador o el catálogo para agregar.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIGHT PANEL: Resumen y Pago -->
    <aside class="sales-card">
        
        <!-- Configuración Documento -->
        <div class="summary-block">
            <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px; display: block;">TIPO DE COMPROBANTE</label>
            <div class="doc-type-switch">
                <div class="doc-type-option active" onclick="setDocType('boleta')" id="btnBoleta">BOLETA</div>
                <div class="doc-type-option" onclick="setDocType('factura')" id="btnFactura">FACTURA</div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b;">SERIE</label>
                    <input type="text" value="B001" readonly style="width: 100%; padding: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; font-weight: 600; color: #475569;" id="docSerie">
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b;">NÚMERO</label>
                    <input type="text" value="Automatico" readonly style="width: 100%; padding: 8px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; color: #94a3b8;">
                </div>
            </div>
        </div>

        <!-- Cliente -->
        <div class="summary-block">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label style="font-size: 12px; font-weight: 600; color: #64748b;">CLIENTE</label>
                <a href="#" style="font-size: 12px; color: #3b82f6; text-decoration: none; font-weight: 600;">Cambiar</a>
            </div>
            
            <div class="client-info">
                <div class="client-row">
                    <span class="client-label">RUC/DNI:</span>
                    <span class="client-value" id="clientDoc">00000000</span>
                </div>
                <div class="client-row">
                    <span class="client-label">Nombre:</span>
                    <span class="client-value" id="clientName">CLIENTE GENERAL</span>
                </div>
                 <div class="client-row">
                    <span class="client-label">Dirección:</span>
                    <span class="client-value" id="clientAddr">-</span>
                </div>
            </div>
        </div>

         <!-- Forma de Pago -->
         <div class="summary-block">
            <label style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px; display: block;">METODO DE PAGO</label>
            <select style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-weight: 500;">
                <option value="EFECTIVO">Efectivo (Contado)</option>
                <option value="YAPE">Yape / Plin</option>
                <option value="TARJETA">Tarjeta Crédito/Débito</option>
                <option value="TRANSFERENCIA">Transferencia</option>
            </select>
         </div>

        <!-- Totales (Push to bottom before actions) -->
        <div class="summary-block" style="margin-top: auto; background: #f8fafc;">
            <div class="totals-grid">
                <div class="total-row">
                    <span>Op. Gravada</span>
                    <span id="lblGravada">S/ 0.00</span>
                </div>
                <div class="total-row">
                    <span>IGV (18%)</span>
                    <span id="lblIgv">S/ 0.00</span>
                </div>
                 <div class="total-row">
                    <span>Descuento Global</span>
                    <span style="color: #ef4444;">- S/ 0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>TOTAL A PAGAR</span>
                    <span id="lblTotal">S/ 0.00</span>
                </div>
            </div>
        </div>

        <!-- Acción Principal -->
        <div class="action-area">
            <div style="display: flex; gap: 10px;">
                <button class="btn-process" style="background: #fff; color: #475569; border: 1px solid #cbd5e1;" onclick="cancelSale()">
                    Cancelar
                </button>
                <button class="btn-process" id="btnProcess" disabled onclick="processSale()">
                    <span>⚡</span> PROCESAR VENTA
                </button>
            </div>
        </div>

    </aside>

</div>

{{-- DATOS TEMPORALES (DEMO) --}}
<script>
    // Inventario Dummy para Demo
    const mockInventory = [
        { id: 1, code: 'P001', name: 'REFLECTOR LED 50W OPALUX', price: 35.00, unit: 'UND' },
        { id: 2, code: 'P002', name: 'CINTA AISLANTE 3M TEMFLEX', price: 4.50, unit: 'ROLLO' },
        { id: 3, code: 'P003', name: 'INTERRUPTOR DOBLE MATRIX', price: 12.00, unit: 'UND' },
        { id: 4, code: 'P004', name: 'CABLE #14 INDECO ROJO', price: 180.00, unit: 'ROLLO' },
        { id: 5, code: 'S001', name: 'SERVICIO DE INSTALACION', price: 50.00, unit: 'ZZ' },
    ];
</script>

<script>
    /**
     * LÓGICA DE VENTAS (Vanilla JS)
     */
    const state = {
        cart: [],
        docType: 'boleta', // boleta | factura
        docSerie: 'B001',
        client: {
            doc: '00000000',
            name: 'CLIENTE VARIOS',
            address: '-'
        }
    };

    // DOM Elements
    const els = {
        cartTable: document.getElementById('cartItems'),
        emptyRow: document.getElementById('emptyRow'),
        inputSearch: document.getElementById('productSearch'),
        lblGravada: document.getElementById('lblGravada'),
        lblIgv: document.getElementById('lblIgv'),
        lblTotal: document.getElementById('lblTotal'),
        btnProcess: document.getElementById('btnProcess'),
        btnBoleta: document.getElementById('btnBoleta'),
        btnFactura: document.getElementById('btnFactura'),
        docSerie: document.getElementById('docSerie'),
        clock: document.getElementById('clock')
    };

    // --- Init ---
    setInterval(() => {
        const now = new Date();
        els.clock.innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }, 1000);

    // --- Search Logic ---
    els.inputSearch.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            const term = e.target.value.toUpperCase();
            const product = mockInventory.find(p => p.code === term || p.name.includes(term));
            
            if (product) {
                addItem(product);
                e.target.value = ''; // clear
            } else {
                // Shake effect or feedback
                alert('Producto no encontrado (Demo: prueba P001, P002...)');
            }
        }
    });

    // --- Cart Actions ---
    function addItem(product) {
        // Check if exists
        const existing = state.cart.find(i => i.id === product.id);
        if (existing) {
            existing.qty++;
        } else {
            state.cart.push({ ...product, qty: 1 });
        }
        renderCart();
    }

    function removeItem(index) {
        state.cart.splice(index, 1);
        renderCart();
    }

    function updateQty(index, newQty) {
        if (newQty < 1) return;
        state.cart[index].qty = parseFloat(newQty);
        renderCart();
    }

    // --- Render ---
    function renderCart() {
        // Clear table (except empty row logic handled visually)
        els.cartTable.innerHTML = '';

        if (state.cart.length === 0) {
            els.cartTable.appendChild(els.emptyRow);
            els.btnProcess.disabled = true;
            updateTotals(0);
            return;
        }

        els.btnProcess.disabled = false;
        let grandTotal = 0;

        state.cart.forEach((item, index) => {
            const total = item.price * item.qty;
            grandTotal += total;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="color: #64748b;">${index + 1}</td>
                <td>
                    <div style="font-weight: 600; color: #0f172a;">${item.name}</div>
                    <div style="font-size: 11px; color: #94a3b8;">${item.code}</div>
                </td>
                <td style="text-align: center;">
                    <span style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">${item.unit}</span>
                </td>
                <td style="text-align: right;">${item.price.toFixed(2)}</td>
                <td style="text-align: center;" class="col-qty">
                    <input type="number" value="${item.qty}" min="1" onchange="updateQty(${index}, this.value)">
                </td>
                <td style="text-align: right; font-weight: 600;">${total.toFixed(2)}</td>
                <td style="text-align: center;">
                    <button class="btn-remove" onclick="removeItem(${index})">✕</button>
                </td>
            `;
            els.cartTable.appendChild(tr);
        });

        updateTotals(grandTotal);
    }

    function updateTotals(total) {
        const subtotal = total / 1.18;
        const igv = total - subtotal;

        els.lblGravada.innerText = 'S/ ' + subtotal.toFixed(2);
        els.lblIgv.innerText = 'S/ ' + igv.toFixed(2);
        els.lblTotal.innerText = 'S/ ' + total.toFixed(2);
    }

    // --- Document Type ---
    window.setDocType = function(type) {
        state.docType = type;
        if(type === 'boleta') {
            els.btnBoleta.classList.add('active');
            els.btnFactura.classList.remove('active');
            state.docSerie = 'B001';
        } else {
            els.btnFactura.classList.add('active');
            els.btnBoleta.classList.remove('active');
            state.docSerie = 'F001';
        }
        els.docSerie.value = state.docSerie;
    }

    // --- Process ---
    window.processSale = function() {
        if(state.cart.length === 0) return;

        const btn = els.btnProcess;
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> PROCESANDO...';

        // Simulate API Call
        setTimeout(() => {
            alert('¡Venta procesada con éxito!\nTotal: ' + els.lblTotal.innerText);
            
            // Reset
            cancelSale();
            
            btn.disabled = true; // disabled bec empty
            btn.innerHTML = originalText;
        }, 1500);
    }

    window.cancelSale = function() {
        if(state.cart.length > 0 && !confirm('¿Estás seguro de cancelar la venta actual?')) return;
        
        state.cart = [];
        renderCart();
        els.inputSearch.value = '';
        els.inputSearch.focus();
    }

    // Expose functions globally for inputs
    window.updateQty = updateQty;
    window.removeItem = removeItem;
    window.cancelSale = cancelSale;
    window.openProductModal = function() {
        alert("Aquí se abriría el modal catálogo completo.");
    };

</script>
@endsection