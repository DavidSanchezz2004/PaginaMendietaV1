<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\PortalAssignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $appUser = $request->user(); // AppUser autenticado (sanctum)
        $deviceId = $request->header('X-Device-Id');

        $rows = PortalAssignment::query()
            ->where('app_user_id', $appUser->id)
            ->where('active', true)
            ->with([
                'portalAccount:id,company_id,portal,status',
                'portalAccount.company:id,ruc,razon_social,direccion_fiscal,departamento,provincia,distrito',
            ])
            ->get();

        // Formato limpio para la app
        $data = $rows->map(function ($a) {
            $pa = $a->portalAccount;
            $c = $pa?->company;

            return [
                'assignment_id' => $a->id,
                'portal' => $pa?->portal,
                'portal_status' => $pa?->status,
                'company' => $c ? [
                    'id' => $c->id,
                    'ruc' => $c->ruc,
                    'razon_social' => $c->razon_social,
                    'distrito' => $c->distrito,
                    'direccion_fiscal' => $c->direccion_fiscal,
                ] : null,
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'device_id' => $deviceId,
            'assignments' => $data,
        ]);
    }
}
