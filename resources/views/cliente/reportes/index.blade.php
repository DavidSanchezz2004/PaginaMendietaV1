@extends('layouts.app-cliente')

@section('title', 'Mis reportes')
@section('topbar_subtitle', 'Reportes')

@section('content')
<div class="m-page">
  <div class="m-top">
    <div>
      <h1 class="m-h1">Mis reportes</h1>
      <div class="m-sub">Reportes publicados por tu empresa</div>
    </div>
  </div>

  @if(!$rows->count())
    <section class="m-panel">
      <div class="m-empty">
        <div class="m-empty-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M7 3h7l3 3v15a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
            <path d="M14 3v4h4"></path>
            <path d="M8 13h8"></path>
            <path d="M8 17h6"></path>
          </svg>
        </div>
        <div class="m-empty-title">Aún no hay reportes publicados</div>
        <div class="m-empty-sub">Cuando el estudio publique reportes para tu empresa, aparecerán aquí.</div>
      </div>
    </section>
  @else
    <section class="m-panel">
      <div class="m-panel-head">
        <div>
          <h3 class="m-panel-title">Reportes disponibles</h3>
          <p class="m-panel-sub">Mostrando {{ $rows->total() }} reporte(s).</p>
        </div>
      </div>

      <div class="m-table-wrap">
        <table class="m-table">
          <thead>
            <tr>
              <th>Título</th>
              <th style="width:170px;">Periodo</th>
              <th style="width:110px;">Acción</th>
            </tr>
          </thead>

          <tbody>
            @foreach($rows as $r)
              @php
                $pm = $r->periodo_mes ? str_pad((string)$r->periodo_mes, 2, '0', STR_PAD_LEFT) : '—';
                $py = $r->periodo_anio ?? '—';
              @endphp

              <tr>
                <td class="m-tmain">
                  <div class="m-title">{{ $r->titulo }}</div>
                </td>

                <td>
                  <span class="m-pill">
                    {{ $pm }} / {{ $py }}
                  </span>
                </td>

                <td>
                <a
  class="m-btn m-btn-primary m-btn-icon"
  href="{{ route('cliente.reportes.ver', $r) }}"
  target="_blank"
  rel="noopener"
>
  <svg
    viewBox="0 0 24 24"
    width="16"
    height="16"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    style="margin-right:6px;"
  >
    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
    <circle cx="12" cy="12" r="3"/>
  </svg>
  Ver
</a>

                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="m-pager">
        {{ $rows->links() }}
      </div>
    </section>
  @endif
</div>

<style>
  :root{
    --m-primary:#053d38;
    --m-primary-2:#34675c;
    --m-accent:#a3ccaa;
    --m-line:rgba(2, 6, 23, .08);
    --m-ink:rgba(15, 23, 42, .92);
    --m-muted:rgba(15, 23, 42, .62);
    --m-bg:#f3f6f6;
    --m-panel:#fff;
  }

  .m-page{
    max-width: 1120px;
    margin: 0 auto;
    padding: 18px 16px 40px;
    font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
  }

  .m-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:14px;
    margin-bottom:14px;
  }

  .m-h1{
    margin:0;
    font-size: 22px;
    font-weight: 900;
    letter-spacing: -0.01em;
    color: rgba(5, 61, 56, .96);
  }

  .m-sub{
    margin-top: 6px;
    font-size: 13px;
    color: var(--m-muted);
    font-weight: 650;
  }

  .m-panel{
    background: var(--m-panel);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 16px;
    overflow:hidden;
    box-shadow: 0 12px 22px rgba(2,6,23,.05);
  }

  .m-panel-head{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(226,232,240,.9);
    background: linear-gradient(180deg, rgba(5,61,56,.05), transparent);
  }

  .m-panel-title{
    margin:0;
    font-size: 15px;
    font-weight: 900;
    color: rgba(15,23,42,.92);
  }

  .m-panel-sub{
    margin:4px 0 0;
    font-size: 12.5px;
    color: var(--m-muted);
    font-weight: 650;
  }

  .m-table-wrap{
    overflow:auto;
    -webkit-overflow-scrolling: touch;
  }

  .m-table{
    width: 100%;
    border-collapse: collapse;
    min-width: 760px;
  }

  .m-table thead th{
    text-align:left;
    padding: 12px 16px;
    font-size: 11px;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: rgba(15,23,42,.55);
    font-weight: 900;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .m-table tbody td{
    padding: 14px 16px;
    border-top: 1px solid rgba(226,232,240,.85);
    font-size: 13.5px;
    font-weight: 650;
    color: rgba(15,23,42,.88);
    vertical-align: middle;
  }

  .m-table tbody tr:hover{
    background: rgba(5,61,56,.03);
  }

  .m-tmain{
    max-width: 520px;
  }

  .m-title{
    font-weight: 850;
    color: rgba(15,23,42,.92);
    overflow:hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .m-pill{
    display:inline-flex;
    align-items:center;
    height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    border: 1px solid rgba(5,61,56,.18);
    background: rgba(5,61,56,.06);
    color: rgba(5,61,56,.95);
    font-weight: 900;
    font-size: 12px;
    font-variant-numeric: tabular-nums;
  }

  .m-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    height: 36px;
    padding: 0 12px;
    border-radius: 12px;
    border: 1px solid rgba(226,232,240,.95);
    background: #fff;
    color: rgba(15,23,42,.88);
    font-weight: 900;
    font-size: 13px;
    text-decoration:none;
    transition: transform .12s ease, background .15s ease, border-color .15s ease;
    white-space: nowrap;
  }

  .m-btn svg{
    width: 18px;
    height: 18px;
    stroke: currentColor;
    opacity: .9;
  }

  .m-btn:hover{
    transform: translateY(-1px);
    border-color: rgba(5,61,56,.25);
    background: rgba(5,61,56,.04);
  }

  .m-btn-primary{
    background: var(--m-primary);
    border-color: rgba(5,61,56,.55);
    color:#fff;
  }
  .m-btn-primary:hover{
    background: rgba(5,61,56,.94);
  }

  .m-pager{
    padding: 12px 16px;
    border-top: 1px solid rgba(226,232,240,.9);
  }

  .m-empty{
    padding: 28px 16px;
    display:grid;
    gap:10px;
    place-items:center;
    text-align:center;
  }

  .m-empty-ico{
    width: 64px;
    height: 64px;
    border-radius: 18px;
    display:grid;
    place-items:center;
    border: 1px solid rgba(5,61,56,.14);
    background: radial-gradient(60px 60px at 35% 25%, rgba(163,204,170,.28), transparent 60%),
                rgba(5,61,56,.04);
  }
  .m-empty-ico svg{ width: 30px; height: 30px; stroke: rgba(5,61,56,.9); }

  .m-empty-title{
    font-weight: 950;
    color: rgba(15,23,42,.92);
  }
  .m-empty-sub{
    max-width: 520px;
    color: var(--m-muted);
    font-weight: 650;
    font-size: 13px;
    line-height: 1.55;
  }

  @media (max-width: 520px){
    .m-page{ padding: 14px 12px 26px; }
    .m-h1{ font-size: 18px; }
    .m-table{ min-width: 720px; }
  }
</style>
@endsection
