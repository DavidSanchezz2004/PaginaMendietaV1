<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use App\Models\PortalAssignment;
use Illuminate\Http\Request;

class ClientPortalAccountController extends Controller
{
  public function store(Request $request)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden registrar portales.'], 403);
    }

    $data = $request->validate([
      'company_id' => ['required','integer','exists:companies,id'],
      'portal' => ['required','in:sunat,sunafil,afp,afpnet'],
      'status' => ['nullable','in:active,inactive'],
    ]);

    // ✅ normalizar afpnet -> afp (porque tu enum DB es afp)
    $portalDb = $data['portal'] === 'afpnet' ? 'afp' : $data['portal'];

    $account = PortalAccount::updateOrCreate(
      ['company_id' => $data['company_id'], 'portal' => $portalDb],
      ['status' => $data['status'] ?? 'active']
    );

    // ✅ assignment automático para que /assignments lo liste y /jobs/execute lo permita
    PortalAssignment::firstOrCreate(
      ['portal_account_id' => $account->id, 'app_user_id' => $u->id],
      ['active' => true, 'assigned_by' => null]
    );

    return response()->json([
      'ok'=>true,
      'portal_account'=>[
        'id'=>$account->id,
        'company_id'=>$account->company_id,
        'portal'=> $portalDb === 'afp' ? 'afpnet' : $portalDb, // devolver amigable
        'status'=>$account->status,
      ],
    ], 201);
  }
}
