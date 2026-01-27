<?php

namespace App\Http\Controllers\Api\Robot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;

class RobotViewerController extends Controller
{
    /**
     * Proxy al robot viewer: GET /api/v1/robot/viewer/{session_id}
     * Hace request a: https://operator.antrixsys.xyz/viewer/{session_id}
     * Retorna el HTML/contenido tal cual
     */
    public function viewer(Request $request, string $session_id)
    {
        try {
            $robotBaseUrl = config('services.robot.base_url', 'https://operator.antrixsys.xyz');
            $apiKey = config('services.robot.api_key');

            if (!$apiKey) {
                return response()->json([
                    'ok' => false,
                    'error' => 'robot_api_key_missing',
                    'message' => 'API key del robot no configurada.',
                ], 500);
            }

            // Sanitizar session_id (alfanumérico, guiones, guiones bajos)
            $session_id = preg_replace('/[^a-zA-Z0-9\-_]/', '', $session_id);

            if (empty($session_id)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'invalid_session_id',
                    'message' => 'session_id inválido.',
                ], 422);
            }

            $url = $robotBaseUrl . '/viewer/' . $session_id;

            \Log::info('RobotViewer proxy request', [
                'session_id' => $session_id,
                'target_url' => $url,
            ]);

            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
                'Accept' => '*/*',
            ])->timeout(30)->get($url);

            if (!$response->successful()) {
                \Log::warning('RobotViewer proxy failed', [
                    'session_id' => $session_id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'ok' => false,
                    'error' => 'robot_viewer_error',
                    'message' => 'Error en robot viewer: HTTP ' . $response->status(),
                ], $response->status());
            }

            // Retornar el contenido del robot viewer (generalmente HTML)
            $contentType = $response->header('Content-Type') ?? 'text/html; charset=utf-8';

            return response(
                $response->body(),
                200,
                [
                    'Content-Type' => $contentType,
                    'X-Powered-By' => 'Robot Viewer Proxy',
                ]
            );

        } catch (\Throwable $e) {
            \Log::error('RobotViewer proxy exception', [
                'session_id' => $session_id ?? 'unknown',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'proxy_exception',
                'message' => 'Error interno en proxy viewer.',
            ], 500);
        }
    }
}
