<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PortalAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientCompanyController extends Controller
{
  public function index(Request $request)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden consultar empresas.'], 403);
    }

    $items = $this->userCompaniesQuery($u)
      ->orderByDesc('companies.id')
      ->paginate(15)
      ->withQueryString();

    return response()->json(['ok'=>true,'items'=>$items]);
  }

  public function store(Request $request)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden registrar empresas.'], 403);
    }

    // ✅ contar empresas distintas por assignments
    $currentCount = DB::table('portal_assignments as pa')
      ->join('portal_accounts as pacc', 'pacc.id', '=', 'pa.portal_account_id')
      ->where('pa.app_user_id', $u->id)
      ->where('pa.active', true)
      ->distinct('pacc.company_id')
      ->count('pacc.company_id');

    // ✅ obtener límite según plan
    $maxCompanies = $u->max_companies ?? $this->getMaxCompaniesByPlan($u->plan);

    if ($currentCount > $maxCompanies) {
      return response()->json([
        'ok'=>false,
        'error'=>'plan_limit',
        'message'=>"Límite alcanzado ({$maxCompanies} empresas). Cambia de plan para registrar más.",
        'plan'=>$u->plan,
        'max_companies'=>$maxCompanies,
      ], 422);
    }

    $data = $request->validate([
      'ruc' => ['required','string','max:20'],
      'razon_social' => ['required','string','max:255'],
      'direccion_fiscal' => ['nullable','string','max:255'],
      'departamento' => ['nullable','string','max:80'],
      'provincia' => ['nullable','string','max:80'],
      'distrito' => ['nullable','string','max:80'],
    ]);

    // ✅ opcional: evitar duplicado por RUC
    $existing = Company::where('ruc', $data['ruc'])->first();
    if ($existing) {
      return response()->json([
        'ok'=>true,
        'company'=>$existing,
        'message'=>'Empresa ya existía, se reutilizó.',
      ]);
    }

    $company = Company::create($data);

    return response()->json(['ok'=>true,'company'=>$company], 201);
  }

  public function show(Request $request, int $companyId)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden consultar empresas.'], 403);
    }

    $company = $this->userCompaniesQuery($u)
      ->where('companies.id', $companyId)
      ->first();

    if (! $company) {
      return response()->json(['ok'=>false,'message'=>'Empresa no encontrada.'], 404);
    }

    return response()->json(['ok'=>true,'company'=>$company]);
  }

  public function update(Request $request, int $companyId)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden actualizar empresas.'], 403);
    }

    $company = $this->userCompaniesQuery($u)
      ->where('companies.id', $companyId)
      ->first();

    if (! $company) {
      return response()->json(['ok'=>false,'message'=>'Empresa no encontrada.'], 404);
    }

    $data = $request->validate([
      'razon_social' => ['sometimes','string','max:255'],
      'direccion_fiscal' => ['sometimes','nullable','string','max:255'],
      'departamento' => ['sometimes','nullable','string','max:80'],
      'provincia' => ['sometimes','nullable','string','max:80'],
      'distrito' => ['sometimes','nullable','string','max:80'],
    ]);

    $company->fill($data);
    $company->save();

    return response()->json(['ok'=>true,'company'=>$company]);
  }

  public function destroy(Request $request, int $companyId)
  {
    /** @var \App\Models\AppUser $u */
    $u = $request->user();

    if ($u->type !== 'cliente') {
      return response()->json(['ok'=>false,'message'=>'Solo clientes pueden eliminar empresas.'], 403);
    }

    $company = $this->userCompaniesQuery($u)
      ->where('companies.id', $companyId)
      ->first();

    if (! $company) {
      return response()->json(['ok'=>false,'message'=>'Empresa no encontrada.'], 404);
    }

    DB::transaction(function () use ($u, $companyId) {
      // PortalAccounts asignados a este cliente para esta empresa
      $portalAccountIds = DB::table('portal_accounts as pacc')
        ->join('portal_assignments as pa', 'pa.portal_account_id', '=', 'pacc.id')
        ->where('pa.app_user_id', $u->id)
        ->where('pa.active', true)
        ->where('pacc.company_id', $companyId)
        ->pluck('pacc.id');

      if ($portalAccountIds->isEmpty()) {
        return;
      }

      // Desactivar assignments del cliente (desvincula la empresa)
      DB::table('portal_assignments')
        ->where('app_user_id', $u->id)
        ->whereIn('portal_account_id', $portalAccountIds)
        ->update(['active' => false]);
    });

    return response()->json(['ok'=>true]);
  }

  // ✅ Límites de plan
  private function getMaxCompaniesByPlan(string $plan): int
  {
    $limits = [
      'starter'  => 1,
      'oro'      => 3,
      'pro'      => 10,
      'empresa'  => 999,
    ];
    return $limits[$plan] ?? 1;
  }

  private function userCompaniesQuery($u)
  {
    return Company::query()
      ->select('companies.*')
      ->join('portal_accounts as pacc', 'pacc.company_id', '=', 'companies.id')
      ->join('portal_assignments as pa', 'pa.portal_account_id', '=', 'pacc.id')
      ->where('pa.app_user_id', $u->id)
      ->where('pa.active', true)
      ->distinct();
  }
}
