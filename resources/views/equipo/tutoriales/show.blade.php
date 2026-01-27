@extends('layouts.app-cliente')

@section('title', 'Tutorial')
@section('topbar_subtitle', 'Tutoriales')

@section('content')
  <a href="{{ route('cliente.novedades.index') }}">← Volver</a>

  <h1 style="margin-top:10px;">{{ $tutorial->title }}</h1>

  <div style="font-size:13px; opacity:.75; margin:6px 0 14px;">
    {{ strtoupper($tutorial->category) }}
    @if($tutorial->duration_label) · {{ $tutorial->duration_label }} @endif
    · {{ optional($tutorial->published_at)->format('Y-m-d') ?? '' }}
  </div>

  @if($tutorial->cover_image_url)
    <div style="margin:12px 0;">
      <img src="{{ $tutorial->cover_image_url }}" alt="{{ $tutorial->title }}" style="max-width:100%; border-radius:12px;">
    </div>
  @endif

  @if($tutorial->excerpt)
    <p style="opacity:.85;">{{ $tutorial->excerpt }}</p>
  @endif

  @if($tutorial->body)
    <hr style="margin:16px 0;">
    <div style="white-space:pre-wrap; line-height:1.6;">
      {{ $tutorial->body }}
    </div>
  @endif

  <hr style="margin:18px 0;">

  <!-- ✅ Botón interno (NO youtube_url directo) -->
  <a href="{{ route('cliente.tutorials.watch', $tutorial) }}">
    Ver tutorial (YouTube)
  </a>

  <div style="font-size:12px; opacity:.7; margin-top:10px;">
    * El video se abre mediante una ruta interna del portal.
  </div>
@endsection
