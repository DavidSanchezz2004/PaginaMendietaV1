<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteJobRequest;
use Illuminate\Support\Facades\Http;

class JobExecuteController extends Controller
{
    public function execute(ExecuteJobRequest $request)
    {
        try {
            $user = $request->user();

            $companyId = (int) $request->input('company_id');
            $portal    = (string) $request->input('portal');   // lo dejamos por compat
            $action    = (string) $request->input('action');

            // 1) Resolver endpoint real del robot según action
            $robotPath = match ($action) {
                'sunat.menu_sol_login'    => '/sunat/login',
                'sunat.declaracion_login' => '/sunat/declaracion/login',
                'sunafil.casilla_login'   => '/sunafil/casilla/login',
                'afpnet.login'            => '/afpnet/login',
                default                   => '/sunat/login',
            };

            $base = rtrim((string) config('services.robot.base_url'), '/');
            $robotUrl = $base . $robotPath;

            // 2) Llamar al robot CON x-api-key (esto arregla tu invalid_api_key)
            $robot = Http::timeout((int) config('services.robot.timeout', 60))
                ->withHeaders([
                    'Accept'    => 'application/json',
                    'x-api-key' => (string) config('services.robot.api_key'),
                ])
                ->post($robotUrl, [
                    // TODO: aquí debes mandar credenciales reales.
                    // El robot exige: ruc, usuario_sol, clave_sol
                    //
                    // Si tú las tienes en DB, reemplaza estas 3 líneas por lo que salga de portalAccount:
                    'ruc'         => (string) $request->input('ruc'),
                    'usuario_sol' => (string) $request->input('usuario_sol'),
                    'clave_sol'   => (string) $request->input('clave_sol'),

                    // meta opcional (no afecta al robot)
                    'company_id'  => $companyId,
                    'operator_id' => $user?->id,
                    'portal'      => $portal,
                    'action'      => $action,
                ]);

            if (!$robot->ok()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'robot_error',
                    'robot_status' => $robot->status(),
                    'robot_body' => $robot->body(),
                ], 500);
            }

            $robotData = $robot->json();

            if (!($robotData['ok'] ?? false) || empty($robotData['session_id'])) {
                return response()->json([
                    'ok' => false,
                    'error' => 'robot_bad_response',
                    'robot_body' => $robotData,
                ], 500);
            }

            return response()->json([
                'ok' => true,
                'session_id' => $robotData['session_id'],
                'viewer_url' => rtrim((string) config('services.robot.viewer_url'), '/')
                    . '/viewer/' . $robotData['session_id'],
                'robot' => [
                    'url' => $robotData['url'] ?? null,
                    'titulo' => $robotData['titulo'] ?? null,
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'internal_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
