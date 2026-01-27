@extends('layouts.app-cliente')

@section('title', $tutorial->title ?? 'Tutorial')
@section('topbar_subtitle', 'Tutoriales')

@section('content')
<div class="page">

  <div class="top">
    <div>
      <h1>{{ $tutorial->title ?? 'Tutorial' }}</h1>
      <div class="sub">
        {{ ucfirst($tutorial->category ?? 'general') }}
        <span style="margin:0 8px;">•</span>
        {{ optional($tutorial->created_at)->format('Y-m-d') ?? '—' }}
      </div>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ route('cliente.tutoriales.index') }}">Volver</a>

      {{-- Launcher interno: NO exponer link real --}}
      <a class="btn" href="{{ route('cliente.tutoriales.ver', $tutorial) }}">
        Ver video
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div style="padding:10px; margin:10px 0; border:1px solid #d6f0dc; background:#f4fff7; border-radius:10px;">
      {{ session('ok') }}
    </div>
  @endif

  @if(session('error'))
    <div style="padding:10px; margin:10px 0; border:1px solid #f3b4b4; background:#fff5f5; border-radius:10px;">
      {{ session('error') }}
    </div>
  @endif

  <section class="panel">
    <div class="panel-head">
      <div>
        <h3 class="panel-title">Descripción</h3>
        <p class="panel-sub">Información del tutorial.</p>
      </div>
    </div>

    <div style="padding:16px;">
      @if(!empty($tutorial->body))
        <div style="white-space:pre-wrap; line-height:1.6;">
          {{ $tutorial->body }}
        </div>
      @else
        <p style="color:#666;">— Sin descripción —</p>
      @endif

      <hr style="margin:16px 0;">

      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <div style="font-size:13px; color:#666;">
          Nota: el video se abre desde el portal (no mostramos el link real).
        </div>

        <a href="{{ route('cliente.tutoriales.ver', $tutorial) }}">
          Abrir video
        </a>
      </div>
    </div>
  </section>

</div>
@endsection
