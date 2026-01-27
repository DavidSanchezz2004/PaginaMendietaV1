<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireMfaForEquipo
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Solo internos
        if (!$user || $user->rol === 'cliente') {
            return $next($request);
        }

        // Permitir rutas del propio MFA para evitar loop
        $allowed = [
            'mfa.challenge',
            'equipo.mfa.setup',
            'equipo.mfa.enable',
            'equipo.mfa.disable',
        ];
        if ($request->route() && in_array($request->route()->getName(), $allowed, true)) {
            return $next($request);
        }

        $mfa = $user->mfa;

        // Si no está habilitado, obligar a configurar
        if (!$mfa || !$mfa->enabled) {
            return redirect()->route('equipo.mfa.setup');
        }

        // Si ya pasó MFA en esta sesión, ok
        if (session('mfa_verified') === true) {
            return $next($request);
        }

        // Si está habilitado, exigir challenge
        return redirect()->route('mfa.challenge')->with('url.intended', $request->fullUrl());
    }
}
