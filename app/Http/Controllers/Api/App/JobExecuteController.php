<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteJobRequest;
use App\Models\PortalAssignment;
use App\Models\PortalJob;
use App\Services\PortalCredentialService;
use App\Services\RobotClient;
use App\Services\RobotWorkerPool;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JobExecuteController extends Controller
{
    public function execute(ExecuteJobRequest $request)
    {
        $workerPool = new RobotWorkerPool();
        $worker = null;
        $job = null;

        try {
            $user = $request->user();

            $companyId = (int) $request->input('company_id');
            $portal    = (string) $request->input('portal', '');
            $action    = (string) $request->input('action');
            $deviceId  = (string) $request->header('X-Device-Id', '');

            // Normalizar portal antes de buscar credenciales
            $portalNorm = $portal !== '' ? $portal : match (true) {
                str_starts_with($action, 'sunat.')   => 'sunat',
                str_starts_with($action, 'sunafil.') => 'sunat',   // SUNAFIL usa credenciales SOL
                str_starts_with($action, 'afpnet.')  => 'afpnet',
                default => 'sunat',
            };

            // 1) Buscar assignment del usuario con credenciales (filtrado por portal)
            $assignment = PortalAssignment::query()
                ->where('app_user_id', $user->id)
                ->where('active', true)
                ->whereHas('portalAccount', function ($q) use ($companyId, $portalNorm) {
                    $q->where('company_id', $companyId)
                      ->where('portal', $portalNorm); // 👈 CLAVE: filtrar por portal correcto
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

            // 4) Iterar workers hasta encontrar uno libre (manejo de race condition)
            $workers = $workerPool->getWorkerList();
            
            if (empty($workers)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'no_workers_configured',
                    'message' => 'No hay workers configurados.',
                ], 503);
            }

            $worker = null;
            $last409 = null;
            $robotClient = new RobotClient();
            
            // Tracking de estados para mensajes precisos
            $allDown = true;
            $anyBusy = false;

            foreach ($workers as $w) {
                $baseUrl = rtrim((string) ($w['base_url'] ?? ''), '/');
                $viewerUrl = rtrim((string) ($w['viewer_url'] ?? ''), '/');

                if ($baseUrl === '' || $viewerUrl === '') {
                    continue;
                }

                // Health check rápido
                $health = $workerPool->checkHealthPublic($baseUrl);
                
                // Worker no responde (caído/apagado)
                if (!$health || !($health['ok'] ?? false)) {
                    Log::debug('[JobExecute] Worker caído o sin respuesta', ['worker' => $baseUrl]);
                    continue;
                }
                
                // Al menos un worker respondió
                $allDown = false;
                
                // Worker ocupado (tiene sesión activa)
                if ($health['session_active'] ?? true) {
                    $anyBusy = true;
                    Log::debug('[JobExecute] Worker ocupado', [
                        'worker' => $baseUrl,
                        'sesiones_activas' => $health['sesiones_activas'] ?? null,
                    ]);
                    continue;
                }

                // Worker libre detectado, intentar login
                $robotClient->setBaseUrl($baseUrl);

                // Payload base
                $payload = [
                    'ruc'         => $ruc,
                    'action'      => $action,
                    'company_id'  => $companyId,
                    'operator_id' => $user->id,
                    'portal'      => $portalNorm,
                ];

                // SOL keys (SUNAT/SUNAFIL)
                $payload['usuario_sol'] = $username;
                $payload['clave_sol']   = $password;

                // AFP keys (AFPnet) - múltiples variantes para compatibilidad
                if ($portalNorm === 'afpnet' || str_starts_with($action, 'afpnet.')) {
                    $payload['usuario']  = $username;
                    $payload['clave']    = $password;
                    $payload['username'] = $username;
                    $payload['password'] = $password;
                }

                $robot = $robotClient->post(
                    $robotPath,
                    $payload,
                    [
                        'x-company-id' => (string) $companyId,
                        'x-portal'     => $portalNorm,
                        'x-device-id'  => $deviceId,
                    ]
                );

                // 409 = race condition (alguien ganó primero), probar siguiente worker
                if ($robot->status() === 409) {
                    $last409 = $robot->json();
                    Log::debug('[JobExecute] Worker ocupado (409), probando siguiente', [
                        'worker' => $baseUrl,
                    ]);
                    continue;
                }

                // Error real del robot (no 409), fallar inmediatamente
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

                // ✅ Éxito! Worker asignado
                $worker = [
                    'base_url' => $baseUrl,
                    'viewer_url' => $viewerUrl,
                    'worker_id' => $robotData['worker_id'] ?? $health['worker_id'] ?? null,
                    'session_id' => $robotData['session_id'],
                    'robot_data' => $robotData,
                ];
                break;
            }

            // Manejo inteligente de errores según el estado detectado
            if (!$worker) {
                if ($allDown) {
                    // Ningún worker respondió al health check
                    return response()->json([
                        'ok' => false,
                        'error' => 'all_workers_down',
                        'message' => 'No hay robots disponibles. Verifica que los servicios estén funcionando.',
                        'last_409' => $last409,
                    ], 503);
                } else if ($anyBusy) {
                    // Al menos un worker respondió pero todos están ocupados
                    return response()->json([
                        'ok' => false,
                        'error' => 'no_workers_available',
                        'message' => 'Todos los robots están ocupados. Intenta en unos segundos.',
                        'last_409' => $last409,
                    ], 503);
                } else {
                    // Caso inesperado (por si acaso)
                    return response()->json([
                        'ok' => false,
                        'error' => 'no_workers_available',
                        'message' => 'No se pudo asignar un robot. Intenta nuevamente.',
                        'last_409' => $last409,
                    ], 503);
                }
            }

            // 5) Crear y guardar PortalJob con toda la info del worker y sesión
            $job = PortalJob::create([
                'job_uid' => Str::upper(Str::random(12)) . '-' . time(),
                'company_id' => $companyId,
                'portal_account_id' => $assignment->portalAccount->id,
                'portal' => $portal,
                'action' => $action,
                'app_user_id' => $user->id,
                'device_id' => $deviceId,
                'status' => 'done',
                'started_at' => now(),
                'finished_at' => now(),
                'robot_worker_base_url' => $worker['base_url'],
                'robot_worker_viewer_url' => $worker['viewer_url'],
                'robot_worker_id' => $worker['worker_id'],
                'robot_session_id' => $worker['session_id'],
                'meta' => [
                    'ip' => $request->ip(),
                    'ua' => substr((string) $request->userAgent(), 0, 200),
                ],
            ]);

            // viewer_url - usar path format para que el robot valide correctamente
            // Formato: https://viewer.example.com/viewer/{session_id}
            $viewerUrl = rtrim($worker['viewer_url'], '/') . '/viewer/' . $worker['session_id'];

            return response()->json([
                'ok' => true,
                'job_id' => $job->id,
                'job_uid' => $job->job_uid,
                'session_id' => $worker['session_id'],
                'worker_id' => $worker['worker_id'],
                'viewer_url' => $viewerUrl,
                'robot' => [
                    'url' => $worker['robot_data']['url'] ?? null,
                    'titulo' => $worker['robot_data']['titulo'] ?? null,
                    'worker' => $worker['base_url'],
                ],
            ]);

        } catch (\Throwable $e) {

            if ($job) {
                $job->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                    'meta' => array_merge((array)($job->meta ?? []), [
                        'exception' => $e->getMessage(),
                    ]),
                ]);
            }

            return response()->json([
                'ok' => false,
                'error' => 'internal_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✅ Cierre de sesión del robot (libera el worker)
     * DELETE /jobs/{id}/close o POST /jobs/{id}/close
     * 
     * CRÍTICO para operación real:
     * - Si el operador cierra la ventana sin terminar → worker queda ocupado
     * - Este endpoint llama a DELETE /sunat/close/{session_id} en el worker
     * - Actualiza el job a status=closed
     */
    public function close($jobId)
    {
        try {
            $user = \request()->user();

            // Buscar el job del usuario
            $job = PortalJob::query()
                ->where('id', $jobId)
                ->where('app_user_id', $user->id)
                ->first();

            if (!$job) {
                return response()->json([
                    'ok' => false,
                    'error' => 'job_not_found',
                    'message' => 'Job no encontrado o no pertenece al usuario'
                ], 404);
            }

            // Verificar que tenga sesión activa
            if (!$job->robot_session_id || !$job->robot_worker_base_url) {
                return response()->json([
                    'ok' => false,
                    'error' => 'no_active_session',
                    'message' => 'Este job no tiene una sesión activa para cerrar'
                ], 400);
            }

            // Ya está cerrado
            if ($job->status === 'closed') {
                return response()->json([
                    'ok' => true,
                    'message' => 'Sesión ya estaba cerrada',
                    'job_id' => $job->id,
                ]);
            }

            // Llamar al robot para cerrar la sesión
            $client = new RobotClient();
            $client->setBaseUrl($job->robot_worker_base_url);

            try {
                $response = $client->delete('/sunat/close/' . $job->robot_session_id, [
                    'x-company-id' => (string) $job->company_id,
                    'x-portal'     => (string) $job->portal,
                    'x-device-id'  => (string) \request()->header('X-Device-Id', ''),
                ]);

                Log::info('Robot session closed', [
                    'job_id' => $job->id,
                    'session_id' => $job->robot_session_id,
                    'worker' => $job->robot_worker_base_url,
                    'response' => $response,
                ]);

            } catch (\Exception $e) {
                // Aunque falle el robot, marcamos el job como cerrado
                Log::warning('Error al cerrar sesión en robot, pero marcamos job como closed', [
                    'job_id' => $job->id,
                    'session_id' => $job->robot_session_id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Actualizar el job
            $job->update([
                'status' => 'closed',
                'finished_at' => now(),
                'meta' => array_merge((array)($job->meta ?? []), [
                    'closed_at' => now()->toIso8601String(),
                    'closed_by' => 'user',
                ]),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Sesión cerrada correctamente',
                'job_id' => $job->id,
                'session_id' => $job->robot_session_id,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al cerrar job', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'close_failed',
                'message' => 'Error al cerrar la sesión: ' . $e->getMessage(),
            ], 500);
        }
    }
}
