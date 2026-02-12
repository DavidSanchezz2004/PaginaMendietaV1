@extends('layouts.app-cliente')

@section('title', 'Reportes y Estadísticas')

@push('styles')
<style>
/* =========================================
   DISEÑO PROFESIONAL / ERP (DASHBOARD)
   ========================================= */

.index-container {
    padding: 24px;
    background-color: #f1f5f9; /* Slate-100 */
    min-height: calc(100vh - 60px);
    font-family: 'Inter', system-ui, sans-serif;
}

/* Header */
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

/* Filters */
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
    min-width: 140px;
}
.filter-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1); }

.btn-secondary {
    background: #fff; color: #475569; border: 1px solid #cbd5e1;
    padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; gap: 8px;
}
.btn-secondary:hover { background: #f8fafc; color: #0f172a; }

/* KPI Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.kpi-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
}

.kpi-title { font-size: 13px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
.kpi-value { font-size: 28px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.kpi-sub { font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 4px; }
.kpi-trend-up { color: #16a34a; font-weight: 600; }
.kpi-trend-mid { color: #d97706; font-weight: 600; }
.kpi-trend-down { color: #dc2626; font-weight: 600; }

/* Charts Section */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.chart-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.chart-title { font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }

.rep-pill {
    display: flex; background: #f1f5f9; border-radius: 6px; padding: 2px;
}
.rep-pill-btn {
    border: none; background: none; padding: 4px 12px; font-size: 12px; font-weight: 600;
    color: #64748b; cursor: pointer; border-radius: 4px; transition: all 0.2s;
}
.rep-pill-btn.is-active { background: #fff; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

/* SVG Chart Override Styles */
.rep-svg { width: 100%; height: 260px; overflow: visible; }
.rep-grid { stroke: #e2e8f0; stroke-width: 1; stroke-dasharray: 4; }
.rep-area { fill: rgba(59, 130, 246, 0.1); }
.rep-line { fill: none; stroke: #3b82f6; stroke-width: 3; stroke-linecap: round; }
.rep-point { fill: #fff; stroke: #3b82f6; stroke-width: 2; }
.rep-x { display: flex; justify-content: space-between; margin-top: 10px; font-size: 11px; color: #94a3b8; }

.rep-area-buy { fill: rgba(244, 63, 94, 0.1); }
.rep-line-buy { fill: none; stroke: #f43f5e; stroke-width: 3; stroke-linecap: round; }
.rep-point-buy { fill: #fff; stroke: #f43f5e; stroke-width: 2; }

</style>
@endpush

@section('content')
<div class="index-container">
    
    <!-- Encabezado -->
    <div class="index-header">
        <h1 class="index-title">
            <i data-lucide="bar-chart-2"></i> Reportes y Estadísticas
        </h1>
        <div style="display: flex; gap: 10px;">
            <button class="btn-secondary">
                <i data-lucide="download"></i> Exportar PDF
            </button>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="filter-bar">
        <div class="filter-group">
            <i data-lucide="calendar" style="width: 18px; color: #64748b;"></i>
            <label style="font-size: 13px; font-weight: 600; color: #0f172a;">Rango:</label>
        </div>
        <div class="filter-group">
            <input type="date" class="filter-input" value="{{ date('Y-02-01') }}">
            <span style="color: #94a3b8;">—</span>
            <input type="date" class="filter-input" value="{{ date('Y-02-28') }}">
        </div>

        <div style="margin-left: auto;">
             <button class="btn-primary" style="background: #0f172a; color: #fff; border:none; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer;" onclick="location.reload()">
                Actualizar
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <!-- Card 1: Ventas Totales -->
        <div class="kpi-card">
            <div class="kpi-title">Ventas Totales (Soles)</div>
            <div class="kpi-value">S/ 373,413.80</div>
            <div class="kpi-sub">
                <span class="kpi-trend-up"><i data-lucide="trending-up" style="width:14px; display:inline;"></i> +12.5%</span>
                vs. mes anterior
            </div>
        </div>

        <!-- Card 2: Compras Totales -->
        <div class="kpi-card">
            <div class="kpi-title">Compras Totales (Soles)</div>
            <div class="kpi-value">S/ 3,236.18</div>
            <div class="kpi-sub">
                <span class="kpi-trend-mid"><i data-lucide="minus" style="width:14px; display:inline;"></i> 0.0%</span>
                estable
            </div>
        </div>

        <!-- Card 3: Balance -->
        <div class="kpi-card">
            <div class="kpi-title">Balance Neto</div>
            <div class="kpi-value" style="color: #2563eb;">S/ 370,177.62</div>
            <div class="kpi-sub">
                Rentabilidad bruta estimada
            </div>
        </div>
        
         <!-- Card 4: Dólares -->
         <div class="kpi-card">
            <div class="kpi-title">Ventas en Dólares</div>
            <div class="kpi-value" style="color: #16a34a;">$ 413.00</div>
            <div class="kpi-sub">
                5 transacciones
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        
        <!-- Vents Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title"><i data-lucide="activity" style="width: 18px; color: #3b82f6;"></i> Evolución de Ventas</div>
                <div class="rep-pill" role="tablist">
                    <button class="rep-pill-btn is-active">Diario</button>
                    <button class="rep-pill-btn">Semanal</button>
                    <button class="rep-pill-btn">Mensual</button>
                </div>
            </div>
            
             <div class="rep-chart">
                <svg viewBox="0 0 980 260" preserveAspectRatio="none" class="rep-svg">
                  {{-- Grid --}}
                  @for($i=0; $i<=10; $i++) <line x1="{{ 40 + $i*90 }}" y1="20" x2="{{ 40 + $i*90 }}" y2="230" class="rep-grid" /> @endfor
                  @for($i=0; $i<=5; $i++)  <line x1="40" y1="{{ 20 + $i*42 }}" x2="940" y2="{{ 20 + $i*42 }}" class="rep-grid" /> @endfor

                  {{-- Area & Line (Blue) --}}
                  <path class="rep-area" d="M40,228 C150,228 190,228 250,226 C320,224 350,40 470,40 C560,40 580,228 650,228 C740,228 800,216 900,210 L940,228 L40,228 Z" />
                  <path class="rep-line" d="M40,228 C150,228 190,228 250,226 C320,224 350,40 470,40 C560,40 580,228 650,228 C740,228 800,216 900,210 L940,228" />
                  
                  {{-- Points --}}
                  <circle cx="40" cy="228" r="4" class="rep-point"/><circle cx="250" cy="226" r="4" class="rep-point"/><circle cx="470" cy="40" r="4" class="rep-point"/><circle cx="650" cy="228" r="4" class="rep-point"/><circle cx="900" cy="210" r="4" class="rep-point"/>
                </svg>
                <div class="rep-x">
                   <span>01 Feb</span><span>03 Feb</span><span>05 Feb</span><span>07 Feb</span><span>09 Feb</span><span>11 Feb</span><span>13 Feb</span><span>15 Feb</span><span>17 Feb</span><span>19 Feb</span><span>21 Feb</span>
                </div>
              </div>
        </div>

        <!-- Compras Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title"><i data-lucide="shopping-cart" style="width: 18px; color: #f43f5e;"></i> Evolución de Compras</div>
                <div class="rep-pill" role="tablist">
                    <button class="rep-pill-btn is-active">Diario</button>
                    <button class="rep-pill-btn">Semanal</button>
                    <button class="rep-pill-btn">Mensual</button>
                </div>
            </div>

            <div class="rep-chart">
                <svg viewBox="0 0 980 260" preserveAspectRatio="none" class="rep-svg">
                  @for($i=0; $i<=10; $i++) <line x1="{{ 40 + $i*90 }}" y1="20" x2="{{ 40 + $i*90 }}" y2="230" class="rep-grid" /> @endfor
                  @for($i=0; $i<=5; $i++)  <line x1="40" y1="{{ 20 + $i*42 }}" x2="940" y2="{{ 20 + $i*42 }}" class="rep-grid" /> @endfor

                  {{-- Area & Line (Red) --}}
                  <path class="rep-area-buy" d="M40,228 C170,228 230,220 300,120 C340,70 390,70 430,120 C520,228 760,228 940,228 L940,228 L40,228 Z" />
                  <path class="rep-line-buy" d="M40,228 C170,228 230,220 300,120 C340,70 390,70 430,120 C520,228 760,228 940,228" />
                  
                  <circle cx="40" cy="228" r="4" class="rep-point-buy"/><circle cx="300" cy="120" r="4" class="rep-point-buy"/><circle cx="365" cy="70" r="4" class="rep-point-buy"/><circle cx="430" cy="120" r="4" class="rep-point-buy"/><circle cx="940" cy="228" r="4" class="rep-point-buy"/>
                </svg>
                 <div class="rep-x">
                   <span>01 Feb</span><span>03 Feb</span><span>05 Feb</span><span>07 Feb</span><span>09 Feb</span><span>11 Feb</span><span>13 Feb</span><span>15 Feb</span><span>17 Feb</span><span>19 Feb</span><span>21 Feb</span>
                </div>
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

    // Interaction for Pills (UI only)
    document.querySelectorAll('.rep-pill-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const parent = this.closest('.rep-pill');
            parent.querySelectorAll('.rep-pill-btn').forEach(b => b.classList.remove('is-active'));
            this.classList.add('is-active');
        });
    });
});
</script>
@endpush