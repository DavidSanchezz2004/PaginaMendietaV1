<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PortalAccount;
use App\Models\PortalAssignment;
use App\Models\PortalCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ClientOnboardController extends Controller
{
    public function store(Request $request)
    {
        /** @var \App\Models\AppUser $u */
        $u = $request->user();

        if ($u->type !== 'cliente') {
            return response()->json([
                'ok' => false,
                'message' => 'Solo clientes pueden registrar empresas.'
            ], 403);
        }

        $data = $request->validate([
            'ruc'            => ['required','string','max:20'],
            'razon_social'   => ['nullable','string','max:255'],
            'direccion_fiscal'=>['nullable','string','max:255'],
            'distrito'       => ['nullable','string','max:80'],

            'portals'        => ['required','array','min:1'],
            'portals.*.portal'   => ['required','in:sunat,sunafil,afp,afpnet'],
            'portals.*.username' => ['nullable','string','max:255'],
            'portals.*.password' => ['required','string','max:255'],
        ]);

        return DB::transaction(function () use ($data, $u) {

            // ---------------- company ----------------
            $company = Company::firstOrCreate(
                ['ruc' => $data['ruc']],
                [
                    'razon_social' => $data['razon_social'] ?? 'Empresa '.$data['ruc'],
                    'direccion_fiscal' => $data['direccion_fiscal'] ?? null,
                    'distrito' => $data['distrito'] ?? null,
                ]
            );

            $created = [];

            // ---------------- portals ----------------
            foreach ($data['portals'] as $p) {

                $portalDb = $p['portal'] === 'afpnet'
                    ? 'afp'
                    : $p['portal'];

                $account = PortalAccount::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'portal' => $portalDb
                    ],
                    ['status' => 'active']
                );

                PortalAssignment::firstOrCreate(
                    [
                        'portal_account_id' => $account->id,
                        'app_user_id' => $u->id
                    ],
                    [
                        'active' => true,
                        'assigned_by' => null
                    ]
                );

                PortalCredential::updateOrCreate(
                    ['portal_account_id' => $account->id],
                    [
                        'username_enc' => !empty($p['username'])
                            ? Crypt::encryptString($p['username'])
                            : null,
                        'password_enc' => Crypt::encryptString($p['password']),
                        'rotated_at' => now(),
                        'updated_by' => null,
                    ]
                );

                $created[] = [
                    'portal' => $p['portal'],
                    'portal_account_id' => $account->id,
                ];
            }

            return response()->json([
                'ok' => true,
                'company' => [
                    'id' => $company->id,
                    'ruc' => $company->ruc,
                    'razon_social' => $company->razon_social,
                ],
                'portals' => $created
            ], 201);
        });
    }
}
