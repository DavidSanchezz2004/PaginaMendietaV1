<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $zona): Response
    {
        // 1) Verificar que esté autenticado (doble seguridad)
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $rol = Auth::user()->rol;

        // 2) Definir roles permitidos por zona
        $rolesPermitidos = match ($zona) {
            'equipo' => [
                'sistemas',
                'gerente_general',
                'supervisor_contable',
                'contador_junior',
            ],
            'cliente' => [
                'cliente', //Se quito los demas roles solo cliente entra aqui
            ],
            default => [],
        };

        // 3) Validar rol
        if (!in_array($rol, $rolesPermitidos, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
