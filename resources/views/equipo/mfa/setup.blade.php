<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar MFA | Portal Mendieta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSS externo (el que ya tienes) --}}
    <link rel="stylesheet" href="{{ asset('css/mfa_codigo.css') }}">
</head>
<body>

<div class="page">

  <div class="top">
    <div>
      <h1>Configurar verificación en dos pasos</h1>
      <div class="sub">
        Por seguridad, es obligatorio activar la verificación en dos pasos (MFA).
      </div>
    </div>
  </div>

  <section class="panel">
    <div class="panel-body">

      {{-- Paso 1 --}}
      <h3>1. Escanea el código QR</h3>
      <p class="sub">
        Usa Google Authenticator, Microsoft Authenticator u otra app compatible.
      </p>

      <div style="margin:14px 0; display:flex; justify-content:center;">
        {!! $qrSvg !!}
      </div>

      {{-- Paso 2 --}}
      <h3 style="margin-top:18px;">2. Ingresa el código de 6 dígitos</h3>

      <form method="POST" action="{{ route('equipo.mfa.enable') }}">
        @csrf

        <label class="lbl">Código MFA</label>
        <input
          class="inp mono"
          type="text"
          name="code"
          inputmode="numeric"
          maxlength="6"
          placeholder="123456"
          required
          autofocus
        >

        @error('code')
          <div style="margin-top:8px; color:#b91c1c; font-weight:700;">
            {{ $message }}
          </div>
        @enderror

        <div style="margin-top:16px;">
          <button class="btn primary" type="submit">
            Activar MFA
          </button>
        </div>
      </form>

      {{-- Recovery codes --}}
      @if (!empty($recoveryCodes))
        <hr style="margin:22px 0;">

        <h3>Códigos de recuperación</h3>
        <p class="sub">
          Guarda estos códigos en un lugar seguro.  
          Cada código se puede usar <b>una sola vez</b>.
        </p>

        <ul style="margin-top:10px;">
          @foreach ($recoveryCodes as $code)
            <li class="mono" style="margin-bottom:6px;">
              {{ $code }}
            </li>
          @endforeach
        </ul>

        <p style="margin-top:10px; font-weight:800; color:#b91c1c;">
          ⚠️ Esta es la única vez que se mostrarán.
        </p>
      @endif

    </div>
  </section>

</div>

</body>
</html>
