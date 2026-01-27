<x-guest-layout>
    <h1>Verificación en dos pasos</h1>

    <p class="subtitle">
        Ingresa el código de tu app autenticadora (6 dígitos)
        o uno de tus códigos de recuperación.
    </p>

    <form class="form" method="POST" action="{{ route('mfa.challenge.verify') }}">
        @csrf

        <div class="field">
            <label for="code">Código</label>
            <input
                id="code"
                class="input"
                type="text"
                name="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                placeholder="123456 o código de recuperación"
                required
                autofocus
            />

            @error('code')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button class="btn" type="submit">Verificar</button>

        <div class="footer">
            Tip: si no tienes el celular, usa un <b>código de recuperación</b>.
        </div>
    </form>
</x-guest-layout>
