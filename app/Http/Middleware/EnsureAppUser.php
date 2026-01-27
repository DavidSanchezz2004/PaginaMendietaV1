<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAppUser
{
    public function handle(Request $request, Closure $next)
    {
        $u = $request->user();

        // Sanctum puede autenticar cualquier tokenable, aquí exigimos AppUser
        if (! $u || ! ($u instanceof \App\Models\AppUser)) {
            return response()->json(['ok' => false, 'message' => 'No autorizado (app_user requerido).'], 401);
        }

        if ($u->status !== 'activo') {
            return response()->json(['ok' => false, 'message' => 'Usuario inactivo.'], 403);
        }

        return $next($request);
    }
}
