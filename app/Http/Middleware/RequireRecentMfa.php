<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireRecentMfa
{
    public function handle(Request $request, Closure $next, int $minutes = 10)
    {
        $ts = (int) session('mfa.recent_at', 0);

        if ($ts > 0 && (time() - $ts) <= ($minutes * 60)) {
            return $next($request);
        }

        // ✅ Si NO es GET, NO guardes intended como PUT/DELETE.
        // Guarda una ruta GET segura para volver.
        $intended = $request->fullUrl();

        if (!$request->isMethod('GET')) {
            $company = $request->route('company');

            if ($request->isMethod('DELETE') && $company) {
                // vuelve a una pantalla de confirmación (GET)
                $intended = route('equipo.empresas.delete', $company);
            } elseif (($request->isMethod('PUT') || $request->isMethod('PATCH')) && $company) {
                // vuelve al edit (GET)
                $intended = route('equipo.empresas.edit', $company);
            } else {
                // fallback seguro
                $intended = url()->previous() ?: route('equipo.empresas.index');
            }
        }

        session(['mfa.intended' => $intended]);

        return redirect()->route('equipo.mfa.confirm.show');
    }
}
