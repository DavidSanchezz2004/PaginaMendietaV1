<x-guest-layout>
    <h1>Iniciar Sesión</h1>
    <p class="subtitle">Ingresa tu correo y contraseña para acceder</p>

    {{-- Session Status (ej. "Password reset link sent") --}}
    <x-auth-session-status class="status" :status="session('status')" />

    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Correo</label>
            <input
                id="email"
                class="input"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="usuario@gmail.com"
                required
                autofocus
                autocomplete="username"
            />
            @error('email')
              <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">Contraseña</label>
            <input
                id="password"
                class="input"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            />
            @error('password')
              <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn" type="submit">Iniciar Sesión</button>

        {{-- Error general opcional (si quieres mostrar algo arriba) --}}
        @if ($errors->has('email'))
            {{-- ya se muestra debajo del input --}}
        @endif

        <div class="footer">
            ¿No tienes cuenta? <a class="link" href="javascript:void(0)">Contacta a sistemas</a>
        </div>
    </form>
</x-guest-layout>
