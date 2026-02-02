<?php

namespace App\Services;

use App\Models\PortalJob;
use Illuminate\Support\Facades\Log;

class RobotSessionService
{
    /**
     * Obtiene el worker asociado a una sesión activa.
     * 
     * Busca el PortalJob con el session_id dado y devuelve la info del worker
     * para que las llamadas posteriores (buzon/list, open, download) vayan al mismo worker.
     * 
     * @param string $sessionId
     * @return array|null ['base_url' => '...', 'viewer_url' => '...'] o null si no se encuentra
     */
    public function getWorkerBySession(string $sessionId): ?array
    {
        $job = PortalJob::query()
            ->where('robot_session_id', $sessionId)
            ->whereNotNull('robot_worker_base_url')
            ->whereNotNull('robot_worker_viewer_url')
            ->first();

        if (!$job) {
            Log::warning('[RobotSessionService] Session no encontrada o sin worker', [
                'session_id' => $sessionId,
            ]);
            return null;
        }

        return [
            'base_url' => $job->robot_worker_base_url,
            'viewer_url' => $job->robot_worker_viewer_url,
            'job_id' => $job->id,
            'company_id' => $job->company_id,
            'portal' => $job->portal,
        ];
    }

    /**
     * Obtiene el worker asociado a un job_id.
     * 
     * @param int $jobId
     * @return array|null ['base_url' => '...', 'viewer_url' => '...', 'session_id' => '...'] o null
     */
    public function getWorkerByJobId(int $jobId): ?array
    {
        $job = PortalJob::query()
            ->where('id', $jobId)
            ->whereNotNull('robot_worker_base_url')
            ->whereNotNull('robot_worker_viewer_url')
            ->first();

        if (!$job) {
            Log::warning('[RobotSessionService] Job no encontrado o sin worker', [
                'job_id' => $jobId,
            ]);
            return null;
        }

        return [
            'base_url' => $job->robot_worker_base_url,
            'viewer_url' => $job->robot_worker_viewer_url,
            'session_id' => $job->robot_session_id,
            'company_id' => $job->company_id,
            'portal' => $job->portal,
        ];
    }

    /**
     * Obtiene la sesión activa para una empresa/portal.
     * Útil para verificar si ya existe una sesión antes de crear una nueva.
     * 
     * @param int $companyId
     * @param string $portal
     * @return array|null ['session_id' => '...', 'worker' => [...], 'job_id' => ...] o null
     */
    public function getActiveSession(int $companyId, string $portal): ?array
    {
        $job = PortalJob::query()
            ->where('company_id', $companyId)
            ->where('portal', $portal)
            ->whereIn('status', ['running', 'done']) // Sesiones activas
            ->whereNotNull('robot_session_id')
            ->whereNotNull('robot_worker_base_url')
            ->latest('started_at')
            ->first();

        if (!$job) {
            return null;
        }

        return [
            'session_id' => $job->robot_session_id,
            'job_id' => $job->id,
            'job_uid' => $job->job_uid,
            'worker' => [
                'base_url' => $job->robot_worker_base_url,
                'viewer_url' => $job->robot_worker_viewer_url,
            ],
            'started_at' => $job->started_at,
        ];
    }

    /**
     * Marca una sesión como finalizada.
     * 
     * @param string $sessionId
     * @return bool
     */
    public function closeSession(string $sessionId): bool
    {
        $job = PortalJob::query()
            ->where('robot_session_id', $sessionId)
            ->first();

        if (!$job) {
            return false;
        }

        $job->update([
            'status' => 'done',
            'finished_at' => now(),
        ]);

        Log::info('[RobotSessionService] Sesión cerrada', [
            'session_id' => $sessionId,
            'job_id' => $job->id,
        ]);

        return true;
    }
}
