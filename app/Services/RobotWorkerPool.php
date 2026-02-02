<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RobotWorkerPool
{
    /**
     * Obtiene un worker libre del pool (primer worker sin sesión activa).
     * Simple y efectivo.
     *
     * @return array|null ['base_url' => '...', 'viewer_url' => '...', 'worker_id' => '...']
     */
    public function getFreeWorker(): ?array
    {
        $workers = $this->getWorkerList();

        if (empty($workers)) {
            Log::warning('[RobotWorkerPool] No hay workers configurados');
            return null;
        }

        foreach ($workers as $worker) {
            $baseUrl = rtrim((string) ($worker['base_url'] ?? ''), '/');
            $viewerUrl = (string) ($worker['viewer_url'] ?? '');

            if ($baseUrl === '' || $viewerUrl === '') {
                continue;
            }

            try {
                $health = $this->checkHealth($baseUrl);

                if (!$health) {
                    Log::warning('[RobotWorkerPool] Worker no disponible', ['worker' => $baseUrl]);
                    continue;
                }

                // ✅ Primer worker libre gana (session_active: false)
                if (($health['ok'] ?? false) && !($health['session_active'] ?? true)) {
                    Log::info('[RobotWorkerPool] Worker libre encontrado', [
                        'worker' => $baseUrl,
                        'worker_id' => $health['worker_id'] ?? null,
                    ]);

                    return [
                        'base_url' => $baseUrl,
                        'viewer_url' => $viewerUrl,
                        'worker_id' => $health['worker_id'] ?? null,
                    ];
                }

            } catch (\Throwable $e) {
                Log::error('[RobotWorkerPool] Error chequeando worker', [
                    'worker' => $baseUrl,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        Log::error('[RobotWorkerPool] No hay workers libres disponibles');
        return null;
    }

    /**
     * Consulta /health con caché corto (1 segundo).
     * Público porque /health no requiere autenticación.
     */
    public function checkHealthPublic(string $baseUrl): ?array
    {
        $cacheKey = 'robot_health:' . md5($baseUrl);

        return Cache::remember($cacheKey, 1, function () use ($baseUrl) {
            try {
                $url = rtrim($baseUrl, '/') . '/health';

                // Sin api-key porque /health es público
                $response = Http::timeout(2)
                    ->connectTimeout(2)
                    ->get($url);

                if (!$response->ok()) {
                    return null;
                }

                $data = $response->json();

                // ✅ Validar estructura REAL del robot: {status, sesiones_activas}
                if (!isset($data['status'])) {
                    return null;
                }

                // Normalizar para que getFreeWorker() funcione igual
                return [
                    'ok' => $data['status'] === 'running',
                    'session_active' => ($data['sesiones_activas'] ?? 0) > 0,
                    'sesiones_activas' => $data['sesiones_activas'] ?? 0,
                    'worker_id' => $data['worker_id'] ?? null,
                    'status' => $data['status'],
                ];

            } catch (\Throwable $e) {
                Log::debug('[RobotWorkerPool] Worker no responde', [
                    'worker' => $baseUrl,
                ]);
                return null;
            }
        });
    }

    /**
     * @deprecated Usar checkHealthPublic() directamente
     */
    protected function checkHealth(string $baseUrl): ?array
    {
        return $this->checkHealthPublic($baseUrl);
    }

    /**
     * Lista de workers desde config o env JSON.
     * Público para que el controller pueda iterar manualmente.
     */
    public function getWorkerList(): array
    {
        $workers = config('services.robot.workers');

        if (is_array($workers) && !empty($workers)) {
            return $workers;
        }

        $workersJson = env('ROBOT_WORKERS');
        if ($workersJson) {
            $decoded = json_decode($workersJson, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $baseUrl = config('services.robot.base_url');
        $viewerUrl = config('services.robot.viewer_url');

        if ($baseUrl && $viewerUrl) {
            return [
                ['base_url' => $baseUrl, 'viewer_url' => $viewerUrl],
            ];
        }

        return [];
    }

    public function getAllWorkersHealth(): array
    {
        $workers = $this->getWorkerList();
        $results = [];

        foreach ($workers as $worker) {
            $baseUrl = (string) ($worker['base_url'] ?? '');
            $viewerUrl = (string) ($worker['viewer_url'] ?? '');
            $health = $baseUrl ? $this->checkHealth($baseUrl) : null;

            $results[] = [
                'base_url' => $baseUrl,
                'viewer_url' => $viewerUrl,
                'health' => $health,
                'available' => $health !== null && ($health['ok'] ?? false),
                'session_active' => $health['session_active'] ?? null,
                'worker_id' => $health['worker_id'] ?? null,
            ];
        }

        return $results;
    }
}
