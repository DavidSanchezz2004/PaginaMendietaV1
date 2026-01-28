<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteJobRequest;
use App\Models\PortalAssignment;
use App\Services\PortalCredentialService;
use Illuminate\Support\Facades\Http;

class JobExecuteController extends Controller
{
    public function execute(ExecuteJobRequest $request)
    {
        try {
            $user = $request->user();

            $companyId = (int) $request->input('company_id');
            $portal    = (string) $request->input('portal');
            $action    = (string) $request->input('action');

            // 1) Buscar assignment del usuario con credenciales
            $assignment = PortalAssignment::query()
                ->where('app_user_id', $user->id)
                ->where('active', true)
                ->whereHas('portalAccount', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->with(['portalAccount.latestCredential', 'portalAccount.company'])
                ->first();

            if (!$assignment) {
                return response()->json([
                    'ok' => false,
                    'error' => 'no_assignment',
                    'message' => 'No tienes asignación activa para esta empresa'
                ], 403);
            }

            $credential = $assignment->portalAccount->latestCredential;
            if (!$credential) {
                return response()->json([
                    'ok' => false,
                    'error' => 'no_credentials',
                    'message' => 'No hay credenciales configuradas para esta cuenta'
                ], 404);
            }

            // 2) Desencriptar credenciales
            $credService = new PortalCredentialService();
            $username = $credService->decryptString($credential->username_enc);
            $password = $credService->decryptString($credential->password_enc);
            $ruc = $assignment->portalAccount->company->ruc ?? '';

            if (!$username || !$password || !$ruc) {
                return response()->json([
                    'ok' => false,
                    'error' => 'invalid_credentials',
                    'message' => 'Credenciales incompletas o inválidas'
                ], 400);
            }

            // 3) Resolver endpoint del robot según action
            $robotPath = match ($action) {
                'sunat.menu_sol_login'    => '/sunat/login',
                'sunat.declaracion_login' => '/sunat/declaracion/login',
                'sunafil.casilla_login'   => '/sunafil/casilla/login',
                'afpnet.login'            => '/afpnet/login',
                default                   => '/sunat/login',
            };

            $base = rtrim((string) config('services.robot.base_url'), '/');
            $robotUrl = $base . $robotPath;

            // 4) Llamar al robot con credenciales reales
            $robot = Http::timeout((int) config('services.robot.timeout', 60))
                ->withHeaders([
                    'Accept'    => 'application/json',
                    'x-api-key' => (string) config('services.robot.api_key'),
                ])
                ->post($robotUrl, [
                    'ruc'         => $ruc,
                    'usuario_sol' => $username,
                    'clave_sol'   => $password,

                    // meta opcional
                    'company_id'  => $companyId,
                    'operator_id' => $user->id,
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
