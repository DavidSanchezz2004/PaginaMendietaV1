<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal Mendieta') }} | Iniciar sesión</title>

    <!-- Poppins (Mendieta) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tu CSS (sin Vite) -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
  <div class="container">
    <!-- LEFT -->
    <div class="left">
      <div class="left-inner">
        {{ $slot }}
      </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
      <!-- Formas decorativas blur -->
      <div class="deco-shape-1"></div>
      <div class="deco-shape-2"></div>
      <div class="deco-shape-3"></div>

      <!-- Líneas decorativas -->
      <div class="lines">
        <svg viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M 50 450 L 200 350 L 350 380 L 500 250 L 650 280 L 750 200"
            stroke="rgba(163,204,170,0.3)"
            stroke-width="2"
            stroke-dasharray="8 8"
          />
          <path
            d="M 100 500 L 250 420 L 400 440 L 550 320 L 700 360"
            stroke="rgba(52,103,92,0.25)"
            stroke-width="2"
            stroke-dasharray="6 6"
          />
          <circle cx="200" cy="350" r="6" fill="rgba(163,204,170,0.4)" />
          <circle cx="500" cy="250" r="6" fill="rgba(163,204,170,0.4)" />
          <circle cx="650" cy="280" r="6" fill="rgba(163,204,170,0.4)" />
        </svg>
      </div>

      <div class="brand">
        <div class="logo">
          <!-- Pon tu logo aquí: public/images/logo.png -->
          <img src="{{ asset('images/logo.png') }}" alt="Logo Mendieta" />
        </div>
        <h2>Portal Estudio Contable Mendieta</h2>
        <p>
          Plataforma interna del Estudio Contable Mendieta para gestión segura
          de clientes, reportes y procesos contables.
        </p>
      </div>
    </div>
  </div>
</body>
</html>
