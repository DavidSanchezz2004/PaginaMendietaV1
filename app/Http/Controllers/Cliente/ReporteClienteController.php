<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\PortalReport;
use App\Models\PortalReportAccessLog;
use Illuminate\Http\Request;

class ReporteClienteController extends Controller
{
    // GET /cliente/reportes
    public function index(Request $request)
    {
        $user = $request->user();

        // Si el cliente no tiene empresa asignada, no mostramos nada
        if (!$user->company_id) {
            return view('cliente.reportes.index', [
                'rows' => collect(),
                'no_company' => true,
            ]);
        }

        $rows = PortalReport::query()
    ->where('company_id', $user->company_id)
    ->where('estado', 'publicado')
    ->orderByDesc('periodo_anio')
    ->orderByDesc('periodo_mes')
    ->orderByDesc('id')
    ->paginate(10)
    ->withQueryString();


        return view('cliente.reportes.index', [
            'rows' => $rows,
            'no_company' => false,
        ]);
    }

    // GET /cliente/reportes/{reporte}/ver
    public function ver(Request $request, PortalReport $reporte)
    {
        $user = $request->user();

        $ok = true;
        $reason = null;

        // Validaciones OBLIGATORIAS
        if (!$user->company_id) {
            $ok = false; $reason = 'user_without_company';
        } elseif ($reporte->estado !== 'publicado') {
            $ok = false; $reason = 'report_not_published';
        } elseif ((int)$reporte->company_id !== (int)$user->company_id) {
            $ok = false; $reason = 'company_mismatch';
        } elseif (empty($reporte->powerbi_url_actual)) {
            $ok = false; $reason = 'missing_powerbi_url';
        }

        // Log SIEMPRE (éxito o fallo)
        PortalReportAccessLog::create([
            'user_id'    => $user->id,
            'company_id' => $user->company_id,
            'reporte_id' => $reporte->id,
            'ok'         => $ok,
            'reason'     => $reason,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
        ]);

        if (!$ok) {
            abort(403);
        }

        // Redirect 302 al link público (sin exponerlo en frontend antes)
        return redirect()->away($reporte->powerbi_url_actual);
    }
}
