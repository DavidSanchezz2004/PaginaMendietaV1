<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use App\Models\PortalAssignment;
use Illuminate\Http\Request;

class ClientPortalAccountController extends Controller
{
  // ✅ Mapea aliases del frontend -> DB
  private array $portalAlias = [
    'afp' => 'afpnet',
  ];

  private function normalizePortal(string $portal): string
  {
    $portal = strtolower(trim($portal));
    return $this->portalAlias[$portal] ?? $portal;
  }

  public function store(Request $request)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json([
        'ok' => false,
        'message' => 'Solo clientes pueden registrar portales.'
      ], 403);
    }

    $data = $request->validate([
      'company_id' => ['required','integer','exists:companies,id'],
      'portal' => ['required','in:sunat,sunafil,afp,afpnet'], // ✅ acepta ambos
      'status' => ['nullable','in:active,inactive'],
    ]);

    // ✅ Normalizar: afp -> afpnet (para que coincida con el ENUM de la DB)
    $portalDb = $this->normalizePortal($data['portal']);

    $account = PortalAccount::updateOrCreate(
      ['company_id' => $data['company_id'], 'portal' => $portalDb],
      ['status' => $data['status'] ?? 'active']
    );

    // ✅ assignment automático
    PortalAssignment::firstOrCreate(
      ['portal_account_id' => $account->id, 'app_user_id' => $u->id],
      ['active' => true, 'assigned_by' => null]
    );

    // ✅ devolver el portal como vino (afp si vino afp, afpnet si vino afpnet)
    return response()->json([
      'ok' => true,
      'portal_account' => [
        'id' => $account->id,
        'company_id' => $account->company_id,
        'portal' => $data['portal'], // devolver lo que envió el frontend
        'status' => $account->status,
      ],
    ], 201);
  }
}