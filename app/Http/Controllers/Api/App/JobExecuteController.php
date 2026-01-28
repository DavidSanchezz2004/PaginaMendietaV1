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
            $portal    = (string) $request->input('portal');
            $action    = (string) $request->input('action');

            // 🔥 LLAMADA REAL AL ROBOT
            $robot = Http::timeout(60)->post(
                config('services.robot.base_url') . "/{$portal}/login",
                [
                    'company_id' => $companyId,
                    'action' => $action,
                    'operator_id' => $user->id,
                ]
            );

            if (!$robot->ok()) {
                return response()->json([
                    'ok' => false,
                    'error' => 'robot_error',
                    'detail' => $robot->body(),
                ], 500);
            }

            $robotData = $robot->json();

            /**
             * EJEMPLO RESPUESTA DEL ROBOT:
             * {
             *   "ok": true,
             *   "session_id": "4965c076",
             *   "url": "https://e-menu.sunat.gob.pe"
             * }
             */

            return response()->json([
                'ok' => true,
                'session_id' => $robotData['session_id'],
                'viewer_url' => config('services.robot.viewer_url')
                    . '/viewer/' . $robotData['session_id'],
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