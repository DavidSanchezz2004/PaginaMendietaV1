<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use App\Models\PortalAssignment;
use App\Models\PortalCredential;
use App\Models\PortalJob;
use App\Models\PortalJobResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    // POST /api/v1/app/jobs
    public function create(Request $request)
{
    $appUser  = $request->user();
    $deviceId = (string) $request->header('X-Device-Id');

    $validated = $request->validate([
        'company_id' => ['required','integer','exists:companies,id'],
        'portal'     => ['required','string', \Illuminate\Validation\Rule::in(['sunat','sunafil','afp'])],
        'action'     => ['required','string','max:80'],
    ]);

    // 1) portal_account activo
    $portalAccount = \App\Models\PortalAccount::query()
        ->where('company_id', $validated['company_id'])
        ->where('portal', $validated['portal'])
        ->where('status', 'active')
        ->first();

    if (! $portalAccount) {
        return response()->json([
            'ok' => false,
            'message' => 'Portal no activo para esta empresa.',
        ], 422);
    }

    // 2) asignación activa
    $assigned = \App\Models\PortalAssignment::query()
        ->where('portal_account_id', $portalAccount->id)
        ->where('app_user_id', $appUser->id)
        ->where('active', true)
        ->exists();

    if (! $assigned) {
        return response()->json([
            'ok' => false,
            'message' => 'No tienes asignación para este portal/empresa.',
        ], 403);
    }

    // 3) token corto
    $execTokenPlain = \Illuminate\Support\Str::random(48);
    $execTokenHash  = \Illuminate\Support\Facades\Hash::make($execTokenPlain);

   $job = PortalJob::create([
    'job_uid' => Str::upper(Str::random(12)).'-'.time(),

    'company_id' => $validated['company_id'],
    'portal_account_id' => $portalAccount->id, // ✅ CLAVE
    'portal' => $validated['portal'],
    'action' => $validated['action'],

    'app_user_id' => $appUser->id,
    'device_id' => $deviceId,

    'status' => 'pending',

    'exec_token_hash' => $execTokenHash,
    'exec_token_expires_at' => now()->addMinutes(10),

    'meta' => [
        'ip' => $request->ip(),
        'ua' => substr((string)$request->userAgent(), 0, 200),
    ],
]);


    return response()->json([
        'ok' => true,
        'job' => [
            'id' => $job->id,
            'job_uid' => $job->job_uid,
            'portal' => $job->portal,
            'action' => $job->action,
            'status' => $job->status,
        ],
        'execution_token' => $execTokenPlain,
        'expires_in_seconds' => 600,
    ]);
}



    // POST /api/v1/app/jobs/secrets
    // body: { job_id, execution_token }
    public function secrets(Request $request)
    {
        $appUser = $request->user();
        $deviceId = (string) $request->header('X-Device-Id');

        $validated = $request->validate([
            'job_id' => ['required','integer','exists:portal_jobs,id'],
            'execution_token' => ['required','string','min:10'],
        ]);

        $job = PortalJob::query()->findOrFail($validated['job_id']);

        // ownership + device binding
        if ((int)$job->app_user_id !== (int)$appUser->id || $job->device_id !== $deviceId) {
            return response()->json([
                'ok' => false,
                'message' => 'Job no pertenece a este usuario/dispositivo.',
            ], 403);
        }

        // token corto: expirado / usado
        if (! $job->exec_token_hash || ! $job->exec_token_expires_at) {
            return response()->json([
                'ok' => false,
                'message' => 'Job no tiene token de ejecución activo.',
            ], 422);
        }

        if (now()->greaterThan($job->exec_token_expires_at)) {
            return response()->json([
                'ok' => false,
                'message' => 'Execution token expirado.',
            ], 401);
        }

        if ($job->exec_token_used_at) {
            return response()->json([
                'ok' => false,
                'message' => 'Execution token ya fue usado.',
            ], 401);
        }

        if (! Hash::check($validated['execution_token'], $job->exec_token_hash)) {
            return response()->json([
                'ok' => false,
                'message' => 'Execution token inválido.',
            ], 401);
        }

        // encontrar portal_account + credenciales
        $portalAccount = PortalAccount::query()
            ->where('company_id', $job->company_id)
            ->where('portal', $job->portal)
            ->first();

        if (! $portalAccount) {
            return response()->json([
                'ok' => false,
                'message' => 'No existe portal_account para este job.',
            ], 422);
        }

        $cred = PortalCredential::query()
            ->where('portal_account_id', $portalAccount->id)
            ->latest('id')
            ->first();

        if (! $cred) {
            return response()->json([
                'ok' => false,
                'message' => 'No hay credenciales registradas para este portal.',
            ], 422);
        }

        // ✅ marcamos token como usado (single-use)
        $job->exec_token_used_at = now();
        $job->status = 'running';
        $job->started_at = $job->started_at ?: now();
        $job->save();

        // ✅ descifrar (password obligatorio)
        $username = $cred->username_enc ? Crypt::decryptString($cred->username_enc) : null;
        $password = Crypt::decryptString($cred->password_enc);

        return response()->json([
            'ok' => true,
            'job_id' => $job->id,
            'portal' => $job->portal,
            'action' => $job->action,
            'credentials' => [
                'username' => $username,
                'password' => $password,
                'extra' => $cred->extra ?? null,
            ],
        ]);
    }

    // POST /api/v1/app/jobs/result
    // body: { job_id, ok, data?, error?, evidence? }
    public function uploadResult(Request $request)
    {
        $appUser = $request->user();
        $deviceId = (string) $request->header('X-Device-Id');

        $validated = $request->validate([
            'job_id' => ['required','integer','exists:portal_jobs,id'],
            'ok' => ['required','boolean'],
            'data' => ['nullable','array'],
            'error' => ['nullable','string'],
            'evidence' => ['nullable','array'], // luego lo cambiamos a uploads reales
        ]);

        $job = PortalJob::query()->findOrFail($validated['job_id']);

        if ((int)$job->app_user_id !== (int)$appUser->id || $job->device_id !== $deviceId) {
            return response()->json([
                'ok' => false,
                'message' => 'Job no pertenece a este usuario/dispositivo.',
            ], 403);
        }

        // guardar resultado
        PortalJobResult::create([
            'portal_job_id' => $job->id,
            'ok' => (bool)$validated['ok'],
            'data' => $validated['data'] ?? null,
            'error' => $validated['error'] ?? null,
            'evidence' => $validated['evidence'] ?? null,
        ]);

        // actualizar estado job
        $job->status = $validated['ok'] ? 'done' : 'failed';
        $job->finished_at = now();
        $job->save();

        return response()->json([
            'ok' => true,
            'job_id' => $job->id,
            'status' => $job->status,
        ]);
    }
}
