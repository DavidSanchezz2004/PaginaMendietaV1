@extends('layouts.app-cliente')

@section('title', 'Noticia')
@section('topbar_subtitle', 'Noticias')

@section('content')
  <a href="{{ route('cliente.novedades.index') }}">← Volver</a>

  <h1 style="margin-top:10px;">{{ $news->title }}</h1>

  <div style="font-size:13px; opacity:.75; margin:6px 0 14px;">
    {{ strtoupper($news->category) }} · {{ optional($news->published_at)->format('Y-m-d') ?? '' }}
  </div>

  @if($news->cover_image_url)
    <div style="margin:12px 0;">
      <img src="{{ $news->cover_image_url }}" alt="{{ $news->title }}" style="max-width:100%; border-radius:12px;">
    </div>
  @endif

  @if($news->excerpt)
    <p style="opacity:.85;">{{ $news->excerpt }}</p>
  @endif

  <hr style="margin:16px 0;">

  <div style="white-space:pre-wrap; line-height:1.6;">
    {{ $news->body }}
  </div>
@endsection
