<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use App\Models\PortalCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ClientPortalCredentialController extends Controller
{
  public function store(Request $request)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden registrar credenciales.'], 403);
    }

    $data = $request->validate([
      'portal_account_id' => ['required','integer','exists:portal_accounts,id'],
      'username' => ['nullable','string','max:255'],     // sunat/sunafil user
      'password' => ['required','string','max:255'],     // sol pass / etc
      'extra'    => ['nullable','array'],                // para afp u otras cosas
    ]);

    // ✅ actualizar o crear credencial (evita duplicados)
    $cred = PortalCredential::updateOrCreate(
      ['portal_account_id' => $data['portal_account_id']],
      [
        'username_enc' => !empty($data['username']) ? Crypt::encryptString($data['username']) : null,
        'password_enc' => Crypt::encryptString($data['password']),
        'extra_enc'    => isset($data['extra']) ? Crypt::encryptString(json_encode($data['extra'])) : null,
        'rotated_at'   => now(),
        'updated_by'   => null,
      ]
    );

    return response()->json([
      'ok'=>true,
      'credential_id'=>$cred->id,
      'message'=>'Credenciales guardadas.',
    ], 201);
  }
}
