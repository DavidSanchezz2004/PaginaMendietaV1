<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssistantController extends Controller
{
    public function show(Request $request)
    {
        return view('cliente.asistente.index');
    }

    public function query(Request $request)
    {
        $user = $request->user();

        // Extra seguridad (aunque ya estás en middleware role:cliente)
        if (!$user || $user->rol !== 'cliente') {
            abort(403);
        }

        $data = $request->validate([
            'text' => ['required', 'string', 'min:2', 'max:1200'],
        ]);

        $url = config('services.n8n.assistant_url');
        if (!$url) {
            return response()->json([
                'ok' => false,
                'error' => 'assistant_not_configured',
                'message' => 'Asistente no configurado.',
            ], 500);
        }

        try {
            $timeout = config('services.n8n.timeout', 25);

            $http = Http::timeout($timeout)
                ->acceptJson()
                ->asJson();

            // Si proteges tu webhook con token:
            $token = (string) config('services.n8n.token');
            if ($token !== '') {
                $http = $http->withHeaders(['X-Portal-Token' => $token]);
            }

            // Stateless: solo mandamos text. Nada de history, nada de metadata sensible.
            $res = $http->post($url, [
                'text' => $data['text'],
            ]);

            if (!$res->ok()) {
                Log::warning('assistant_n8n_bad_response', [
                    'user_id' => $user->id,
                    'status' => $res->status(),
                    'body' => mb_substr((string) $res->body(), 0, 1000),
                ]);

                return response()->json([
                    'ok' => false,
                    'error' => 'assistant_failed',
                    'message' => 'No se pudo consultar al asistente. Intenta nuevamente.',
                ], 502);
            }

            $json = $res->json();

            // Normaliza la salida para tu frontend (por si n8n cambia)
            return response()->json([
                'ok' => (bool)($json['ok'] ?? true),
                'answer' => (string)($json['answer'] ?? ''),
                'sources' => is_array($json['sources'] ?? null) ? $json['sources'] : [],
            ]);
        } catch (\Throwable $e) {
            Log::error('assistant_n8n_exception', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'assistant_exception',
                'message' => 'Error temporal consultando al asistente.',
            ], 500);
        }
    }
}
