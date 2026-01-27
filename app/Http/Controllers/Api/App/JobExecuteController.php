<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExecuteJobRequest;
use App\Models\PortalAssignment;

class JobExecuteController extends Controller
{
    public function execute(ExecuteJobRequest $request)
    {
        \Log::info('JobExecute: Request validation passed');
        
        try {
            $user = $request->user();
            \Log::info('JobExecute: Got user', ['user_id' => $user?->id]);
            
            if (!$user) {
                return response()->json(['ok' => false, 'error' => 'no_user'], 401);
            }

            $deviceId = (string) $request->header('X-Device-Id');
            $companyId = (int) $request->input('company_id');
            $portal    = (string) $request->input('portal');
            $action    = (string) $request->input('action');

            \Log::info('JobExecute: Starting', compact('companyId', 'portal', 'action'));

            // Early check: verify assignment exists
            $assignment = PortalAssignment::query()
                ->where('app_user_id', $user->id)
                ->where('active', true)
                ->with('portalAccount')
                ->first();

            if (!$assignment) {
                \Log::warning('JobExecute: No assignment found', ['user_id' => $user->id]);
                return response()->json(['ok' => false, 'error' => 'no_assignment'], 403);
            }

            \Log::info('JobExecute: Assignment found', ['assignment_id' => $assignment->id]);

            return response()->json([
                'ok' => true,
                'message' => 'Job execute validated successfully',
                'assignment_id' => $assignment->id,
            ]);

        } catch (\Throwable $e) {
            \Log::error('JobExecute CRITICAL ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'error' => 'internal_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
