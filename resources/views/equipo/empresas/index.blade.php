@extends('layouts.app-equipo')

@section('title', 'Empresas')
@section('topbar_subtitle', 'Empresas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/empresas_listar.css') }}">
@endpush

@section('content')
<div class="page">
  <div class="top">
    <div>
      <h1>Empresas</h1>
      <div class="sub">Listado general</div>
    </div>

    <div class="actions">
      <a class="btn primary" href="{{ route('equipo.empresas.create') }}">Nueva empresa</a>
    </div>
  </div>

  @if(session('ok'))
    <div class="alert-ok">{{ session('ok') }}</div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Empresas registradas</h3>
        <p class="panel-sub">Mostrando {{ $companies->total() }} registro(s).</p>
      </div>
    </div>

    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr>
            <th>RUC</th>
            <th>Razón social</th>
            <th>Distrito</th>
            <th>Estado SUNAT</th>
            <th>Condición</th>
            <th>Estado</th>
            <th>Asignado</th>
            <th style="width:110px;">Acciones</th>
          </tr>
        </thead>

        <tbody>
          @forelse($companies as $c)
            <tr>
              <td data-label="RUC" class="mono">{{ $c->ruc }}</td>

              <td data-label="Razón social" class="tmain">
                {{ $c->razon_social }}
              </td>

              <td data-label="Distrito">{{ $c->distrito ?? '—' }}</td>

              <td data-label="Estado SUNAT">
                <span class="status {{ ($c->sunat_estado === 'ACTIVO') ? 'ok' : 'off' }}">
                  {{ $c->sunat_estado ?? '—' }}
                </span>
              </td>

              <td data-label="Condición">
                <span class="status {{ ($c->sunat_condicion === 'HABIDO') ? 'ok' : 'warn' }}">
                  {{ $c->sunat_condicion ?? '—' }}
                </span>
              </td>

              <td data-label="Estado">
                <span class="status {{ ($c->estado_interno === 'Activo') ? 'ok' : (($c->estado_interno === 'Pendiente') ? 'warn' : 'off') }}">
                  {{ $c->estado_interno }}
                </span>
              </td>

              <td data-label="Asignado">
                {{ $c->assignedUser?->name ?? '—' }}
              </td>

              <td class="actions-cell">
                <a class="icon-btn" href="{{ route('equipo.empresas.show', $c) }}" title="Ver">
                  <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </a>

                <a class="icon-btn" href="{{ route('equipo.empresas.edit', $c) }}" title="Editar">
                  <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                  </svg>
                </a>

                <form method="POST" action="{{ route('equipo.empresas.destroy', $c) }}" class="del-form" style="display:inline">
                  @csrf
                  @method('DELETE')

                  <button
                    class="icon-btn danger js-delete"
                    type="button"
                    title="Eliminar"
                    data-name="{{ $c->razon_social }}"
                    data-ruc="{{ $c->ruc }}"
                  >
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                      <path d="M3 6h18"></path>
                      <path d="M8 6V4h8v2"></path>
                      <path d="M6 6l1 16h10l1-16"></path>
                      <path d="M10 11v6"></path>
                      <path d="M14 11v6"></path>
                    </svg>
                  </button>
                </form>

                
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="padding:18px;">Aún no hay empresas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pager">
      {{ $companies->links() }}
    </div>
  </section>
</div>

{{-- Modal FUERA del table-wrap --}}
<div class="m-modal" id="delModal" aria-hidden="true">
  <div class="m-backdrop" data-close></div>

  <div class="m-card" role="dialog" aria-modal="true" aria-labelledby="delTitle">
    <div class="m-head">
      <div class="m-ico">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 9v4"></path>
          <path d="M12 17h.01"></path>
          <path d="M10.3 4.3h3.4L21 21H3L10.3 4.3z"></path>
        </svg>
      </div>

      <div class="m-text">
        <h3 id="delTitle">Confirmar eliminación</h3>
        <p id="delDesc" class="m-sub">
          ¿Seguro de eliminar esta empresa? Esta acción no se puede deshacer.
        </p>
      </div>
    </div>

    <div class="m-body">
      <div class="m-row">
        <span class="m-k">Empresa</span>
        <span class="m-v" id="delCompany">—</span>
      </div>
      <div class="m-row">
        <span class="m-k">RUC</span>
        <span class="m-v mono" id="delRuc">—</span>
      </div>
    </div>

    <div class="m-actions">
      <button class="btn" type="button" id="delCancel">Cancelar</button>
      <button class="btn primary danger" type="button" id="delConfirm">Sí, eliminar</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('delModal');
  const elCompany = document.getElementById('delCompany');
  const elRuc = document.getElementById('delRuc');
  const btnCancel = document.getElementById('delCancel');
  const btnConfirm = document.getElementById('delConfirm');

  let currentForm = null;

  function openModal() {
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    currentForm = null;
  }

  document.querySelectorAll('.js-delete').forEach(btn => {
    btn.addEventListener('click', () => {
      elCompany.textContent = btn.dataset.name || '—';
      elRuc.textContent = btn.dataset.ruc || '—';
      currentForm = btn.closest('form');
      openModal();
    });
  });

  btnCancel.addEventListener('click', closeModal);

  btnConfirm.addEventListener('click', () => {
    if (currentForm) currentForm.submit();
  });

  modal.addEventListener('click', (e) => {
    if (e.target.matches('[data-close]')) closeModal();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
  });
});
</script>
@endpush
