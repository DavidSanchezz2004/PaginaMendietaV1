<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Services\RobotClient;
use App\Services\RobotSessionService;
use Illuminate\Http\Request;

/**
 * Ejemplo de controller para endpoints de buzón SUNAT
 * que reutilizan el worker de la sesión activa
 */
class BuzonController extends Controller
{
    /**
     * GET /api/v1/app/buzon/list
     * 
     * Lista documentos en el buzón.
     * Requiere: session_id (o job_id) para saber qué worker usar.
     */
    public function list(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required_without:job_id', 'string'],
            'job_id' => ['required_without:session_id', 'integer'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $sessionService = new RobotSessionService();

        // Obtener worker según session_id o job_id
        if (!empty($validated['session_id'])) {
            $worker = $sessionService->getWorkerBySession($validated['session_id']);
            $sessionId = $validated['session_id'];
        } else {
            $workerData = $sessionService->getWorkerByJobId($validated['job_id']);
            $worker = $workerData ? [
                'base_url' => $workerData['base_url'],
                'viewer_url' => $workerData['viewer_url'],
            ] : null;
            $sessionId = $workerData['session_id'] ?? null;
        }

        if (!$worker || !$sessionId) {
            return response()->json([
                'ok' => false,
                'error' => 'session_not_found',
                'message' => 'Sesión no encontrada o expirada',
            ], 404);
        }

        // ✅ Llamar al mismo worker donde vive la sesión
        $robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);

        $response = $robotClient->get('/sunat/buzon/list', [
            'session_id' => $sessionId,
            'page' => $validated['page'] ?? 1,
            'per_page' => $validated['per_page'] ?? 20,
        ], [
            'x-company-id' => $request->user()->company_id ?? null,
            'x-portal' => 'sunat',
        ]);

        if (!$response->ok()) {
            return response()->json([
                'ok' => false,
                'error' => 'robot_error',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 500);
        }

        return response()->json($response->json());
    }

    /**
     * POST /api/v1/app/buzon/open
     * 
     * Abre un documento específico.
     */
    public function open(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
            'document_id' => ['required', 'string'],
        ]);

        $sessionService = new RobotSessionService();
        $worker = $sessionService->getWorkerBySession($validated['session_id']);

        if (!$worker) {
            return response()->json([
                'ok' => false,
                'error' => 'session_not_found',
                'message' => 'Sesión no encontrada',
            ], 404);
        }

        $robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);

        $response = $robotClient->post('/sunat/buzon/open', [
            'session_id' => $validated['session_id'],
            'document_id' => $validated['document_id'],
        ], [
            'x-company-id' => $worker['company_id'] ?? null,
            'x-portal' => 'sunat',
        ]);

        if (!$response->ok()) {
            return response()->json([
                'ok' => false,
                'error' => 'robot_error',
                'status' => $response->status(),
            ], 500);
        }

        return response()->json($response->json());
    }

    /**
     * POST /api/v1/app/buzon/download
     * 
     * Descarga un archivo (XML/PDF).
     */
    public function download(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
            'file_token' => ['required', 'string'],
        ]);

        $sessionService = new RobotSessionService();
        $worker = $sessionService->getWorkerBySession($validated['session_id']);

        if (!$worker) {
            return response()->json([
                'ok' => false,
                'error' => 'session_not_found',
            ], 404);
        }

        $robotClient = (new RobotClient())->setBaseUrl($worker['base_url']);

        // GET /files/{token}
        $response = $robotClient->get('/files/' . $validated['file_token'], [], [
            'x-company-id' => $worker['company_id'] ?? null,
        ]);

        if (!$response->ok()) {
            return response()->json([
                'ok' => false,
                'error' => 'file_not_found',
            ], 404);
        }

        // Devolver el archivo directamente
        return response($response->body())
            ->header('Content-Type', $response->header('Content-Type'))
            ->header('Content-Disposition', $response->header('Content-Disposition'));
    }

    /**
     * POST /api/v1/app/session/close
     * 
     * Cierra una sesión manualmente.
     */
    public function closeSession(Request $request)
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $sessionService = new RobotSessionService();
        $success = $sessionService->closeSession($validated['session_id']);

        if (!$success) {
            return response()->json([
                'ok' => false,
                'error' => 'session_not_found',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Sesión cerrada correctamente',
        ]);
    }
}
