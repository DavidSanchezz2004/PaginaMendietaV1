@extends('layouts.app-cliente')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('title', 'Perfil')
@section('topbar_subtitle', 'Mi Perfil')

@section('content')
@php
  $u = auth()->user();
  $p = $u->profile;

  $initials = collect(explode(' ', trim($u->name)))
      ->filter()
      ->map(fn($w) => mb_substr($w, 0, 1))
      ->take(2)
      ->join('');
@endphp

<div class="perfil-page">

  <div class="perfil-titlebar">
    <div>
      <h1>Mi Perfil</h1>
      <div class="perfil-breadcrumb">
        Inicio <span class="sep">›</span> <b>Perfil</b>
      </div>
    </div>

    <div class="perfil-actions">
        <button class="perfil-btn" type="button" disabled title="Próximamente">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 20h9"></path>
          <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        Editar
      </button>
    </div>
  </div>

  <section class="perfil-section">
    <div class="perfil-profile-row">
      <div class="perfil-avatar">{{ $initials ?: 'PM' }}</div>

      <div class="perfil-pinfo">
        <p class="name">{{ $u->name }}</p>
        <p class="meta">
          Cliente &nbsp; | &nbsp; {{ $p?->city ?? '—' }}, {{ $p?->country ?? '—' }}.
        </p>

        <div class="perfil-meta-row">
          <span class="perfil-mitem">
            <span class="perfil-dot ok"></span>
            Activo
          </span>

          <span class="sep">•</span>

          <span class="perfil-mitem">
            Acceso cliente
          </span>

          <span class="sep">•</span>

          <span class="perfil-mitem">
            Email {{ $u->hasVerifiedEmail() ? 'verificado' : 'pendiente' }}
          </span>
        </div>
      </div>
    </div>
  </section>

  <section class="perfil-section">
    <div class="perfil-section-head">
      <h2>Información personal</h2>
        <button class="perfil-btn" type="button" disabled title="Próximamente">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 20h9"></path>
          <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        Editar
      </button>
    </div>

    <div class="perfil-fields">
      <div class="perfil-field">
        <div class="k">Nombres</div>
        <div class="v">{{ explode(' ', $u->name)[0] ?? $u->name }}</div>
      </div>

      <div class="perfil-field">
        <div class="k">Apellidos</div>
        <div class="v muted">—</div>
      </div>

      <div class="perfil-field">
        <div class="k">Correo</div>
        <div class="v">{{ $u->email }}</div>
      </div>

      <div class="perfil-field">
        <div class="k">Teléfono</div>
        <div class="v muted">{{ $p?->phone ?? '—' }}</div>
      </div>

      <div class="perfil-field perfil-full">
        <div class="k">Bio</div>
        <div class="v muted">{{ $p?->bio ?? '—' }}</div>
      </div>
    </div>
  </section>

  <section class="perfil-section">
    <div class="perfil-section-head">
      <h2>Dirección</h2>
      <button class="perfil-btn" type="button" disabled title="Próximamente">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
          <path d="M12 20h9"></path>
          <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        Editar
      </button>
    </div>

    <div class="perfil-fields">
      <div class="perfil-field">
        <div class="k">País</div>
        <div class="v">{{ $p?->country ?? '—' }}</div>
      </div>

      <div class="perfil-field">
        <div class="k">Ciudad</div>
        <div class="v">{{ $p?->city ?? '—' }}</div>
      </div>

      <div class="perfil-field">
        <div class="k">Código Postal</div>
        <div class="v muted">{{ $p?->postal_code ?? '—' }}</div>
      </div>

      @php
        $docType = $p?->document_type ? strtoupper($p?->document_type) : '—';
        $docNum  = $p?->document_number ?? '—';
      @endphp

      <div class="perfil-field">
        <div class="k">Documento</div>
        <div class="v muted">{{ $docType }} / {{ $docNum }}</div>
      </div>
    </div>
  </section>

</div>
@endsection
