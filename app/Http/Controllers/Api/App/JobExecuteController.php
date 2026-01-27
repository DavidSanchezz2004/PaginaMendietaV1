<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteJobRequest;
use App\Models\PortalAccount;
use App\Models\PortalAssignment;
use App\Models\PortalCredential;
use App\Models\PortalJob;
use App\Models\PortalJobResult;
use App\Services\RobotClient;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class JobExecuteController extends Controller
{
    public function execute(ExecuteJobRequest $request, RobotClient $robot)
    {
        try {
            $user = $request->user();
            $deviceId = (string) $request->header('X-Device-Id');  // Obtener ID del dispositivo del header

            $companyId = (int) $request->input('company_id');
            $portal    = (string) $request->input('portal');   // sunat|sunafil|afpnet
            $action    = (string) $request->input('action');   // login|...
            $mode      = (string) ($request->input('mode') ?: 'sync');
            $meta      = (array)  ($request->input('meta') ?: []);

            \Log::info('JobExecute: Starting', compact('companyId', 'portal', 'action', 'mode'));

        // 1) Validar assignment (user puede operar esa empresa/portal)
        // Verificar que el usuario autenticado tenga asignación activa
        // para el portal_account que corresponde a esta empresa/portal
        $assignment = PortalAssignment::query()
            ->where('app_user_id', $user->id)  // Filtrar por usuario autenticado
            ->where('active', true)             // Asignación debe estar activa
            ->with('portalAccount')             // Cargar relación para validar empresa/portal
            ->get()
            ->first(function ($a) use ($companyId, $portal) {
                // Normalizar nombres de portal (case-insensitive y trim)
                $dbPortal = strtolower(trim((string) ($a->portalAccount->portal ?? '')));
                $reqPortal = strtolower(trim((string) $portal));

                // ✅ Crear alias para AFP (afp y afpnet se consideran iguales)
                if ($dbPortal === 'afp') $dbPortal = 'afpnet';
                if ($reqPortal === 'afp') $reqPortal = 'afpnet';

                // Validar que el portalAccount asociado sea de la empresa/portal solicitada
                return (int) $a->portalAccount->company_id === (int) $companyId
                    && $dbPortal === $reqPortal;
            });

        // Si no existe asignación válida, retornar error
        if (!$assignment) {
            return response()->json([
                'ok' => false,
                'error' => 'assignment_denied',
                'message' => 'No tienes asignación activa para esta empresa/portal.',
            ], 403);
        }

        // 2) Encontrar PortalAccount de esa empresa/portal
        // Normalizar portal y crear aliases para variantes (afpnet/afp)
        $portalNorm = strtolower(trim($portal));
        $portalDbCandidates = match ($portalNorm) {
            'afpnet' => ['afpnet', 'afp'],
            'afp'    => ['afpnet', 'afp'],
            default  => [$portalNorm],
        };

        // Tolerar diferentes variantes de status (active, activo, etc.)
        $statusCandidates = ['active', 'activo'];

        $account = PortalAccount::query()
            ->where('company_id', $companyId)
            ->whereIn('portal', $portalDbCandidates)
            ->whereIn('status', $statusCandidates)
            ->with(['latestCredential', 'company'])  // Cargar company para obtener RUC
            ->first();

        if (!$account) {
            return response()->json([
                'ok' => false,
                'error' => 'portal_account_missing',
                'message' => 'La empresa no tiene portal_account activo para este portal.',
            ], 422);
        }

        // 3) Obtener credencial (rotada)
        $cred = $account->latestCredential;

        if (!$cred || empty($cred->password_enc)) {
            return response()->json([
                'ok' => false,
                'error' => 'credentials_missing',
                'message' => 'No hay credenciales registradas para este portal.',
            ], 422);
        }

        $username = $cred->username_enc ? Crypt::decryptString($cred->username_enc) : null;
        $password = Crypt::decryptString($cred->password_enc);

        // 4) Crear PortalJob (registro de ejecución)
        // Generar identificador único para el job (12 caracteres aleatorios + timestamp)
        $jobUid = Str::upper(Str::random(12)) . '-' . time();
        
        $job = PortalJob::create([
            'job_uid'            => $jobUid,                                           // Identificador único del job
            'company_id'         => $companyId,                                        // Empresa solicitante
            'portal_account_id'  => $account->id,                                      // Portal account asociado
            'portal'             => $portal,                                           // Portal (sunat, sunafil, afp)
            'action'             => $action,                                           // Acción a ejecutar
            'app_user_id'        => $user->id,                                         // Usuario autenticado que ejecuta el job
            'device_id'          => $deviceId,                                         // ID del dispositivo desde el que se ejecuta
            'status'             => $mode === 'async' ? 'queued' : 'running',         // Estado inicial según modo
            'meta'               => $meta,                                             // Metadatos adicionales (json)
        ]);

        // Si async: lo normal sería mandar a queue. Por ahora lo dejamos sync.
        // Igual devolvemos job_id siempre.
        if ($mode === 'async') {
            return response()->json([
                'ok' => true,
                'job_id' => $job->id,
                'status' => $job->status,
                'message' => 'Job encolado (pendiente de worker/queue).',
            ]);
        }

        // 5) Llamar al robot (switch por portal/acción)
        //    Puedes mapear acciones a endpoints reales.
        $endpoint = $this->resolveRobotEndpoint($portal, $action);

        // Obtener RUC de la empresa
        $ruc = (string) optional($account->company)->ruc;

        // Construir payload adaptado según el portal
        // Para SUNAT: usa usuario_sol y clave_sol
        $payload = [
            'ruc' => $ruc,
            'usuario_sol' => $username,   // Usuario SOL para SUNAT
            'clave_sol' => $password,     // Contraseña SOL para SUNAT
            'job_id' => $job->id,
            'meta' => $meta ?? [],
        ];

        // Si el RUC está vacío, intentar obtenerlo de la relación company
        if (empty($payload['ruc'])) {
            $payload['ruc'] = (string) $account->company?->ruc;
        }

        // opcional captcha manual (si lo estás usando)
        if ($request->filled('captcha')) {
            $payload['captcha'] = (string) $request->input('captcha');
        }

        try {
            $res = $robot->post($endpoint, $payload);
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed']);

            // Guarda resultado de error (sin reventar tu BD con 20000 líneas)
            $err = mb_substr($e->getMessage(), 0, 2000);

            $this->storeJobResultSafe($job->id, false, null, $err, null);

            return response()->json([
                'ok' => false,
                'job_id' => $job->id,
                'error' => 'robot_exception',
                'message' => $err,
            ], 502);
        }

        // Captura la respuesta del robot
        $body = $res->json();
        $rawBody = $res->body();  // Obtener el body sin parsear
        
        // Log para debuggear la respuesta
        \Log::info('Robot response', [
            'status_code' => $res->status(),
            'body' => $rawBody,
            'parsed_json' => $body,
        ]);
        
        $ok = (bool) data_get($body, 'ok', $res->ok());

        // screenshot base64 puede ser enorme -> guardarlo en evidence aparte o truncarlo
        $evidence = null;
        if (is_array($body) && isset($body['screenshot'])) {
            $evidence = [
                'screenshot' => $body['screenshot'], // si quieres guardarlo aquí, OK pero crece
            ];
            // Si prefieres NO guardarlo en DB: comenta estas 3 líneas y deja evidencia null.
        }

        $job->update(['status' => $ok ? 'done' : 'failed']);

        $this->storeJobResultSafe(
            $job->id,
            $ok,
            $body,
            $ok ? null : (string) (data_get($body, 'error') ?: data_get($body, 'message')),
            $evidence
        );

        return response()->json([
            'ok' => $ok,
            'job_id' => $job->id,
            'status' => $job->status,
            'data' => $body,
        ], $ok ? 200 : 422);

        } catch (\Throwable $e) {
            \Log::error('JobExecute CRITICAL ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'internal_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function resolveRobotEndpoint(string $portal, string $action): string
    {
        // Mapa simple. Ajusta a tus endpoints reales.
        return match ($portal) {
            'sunat'   => match ($action) {
                'login' => '/sunat/login',
                default => '/sunat/login',
            },
            'sunafil' => match ($action) {
                'login' => '/sunafil/casilla/login',
                default => '/sunafil/casilla/login',
            },
            'afpnet'  => match ($action) {
                'login' => '/afpnet/login',
                default => '/afpnet/login',
            },
            default => '/sunat/login',
        };
    }

    private function storeJobResultSafe(int $portalJobId, bool $ok, $data, ?string $error, $evidence): void
    {
        // OJO con tu error previo:
        // tu tabla tiene `evidences` (plural) y NO tiene `error`.
        // Esto lo hacemos “tolerante” para que NO reviente.

        $payload = [
            'portal_job_id' => $portalJobId,
            'ok' => $ok ? 1 : 0,
            'data' => $data,
        ];

        // evidencia si tu columna se llama evidences
        if ($evidence !== null) {
            $payload['evidences'] = $evidence;
        }

        // si tu tabla sí tiene 'error', lo setea; si no, lo mete dentro de data['error']
        $tableCols = \Schema::getColumnListing('portal_job_results');
        if (in_array('error', $tableCols, true)) {
            $payload['error'] = $error ? mb_substr($error, 0, 4000) : null;
        } else {
            if (is_array($payload['data'])) {
                $payload['data']['error'] = $error ? mb_substr($error, 0, 4000) : null;
            }
        }

        PortalJobResult::create($payload);
    }
}
