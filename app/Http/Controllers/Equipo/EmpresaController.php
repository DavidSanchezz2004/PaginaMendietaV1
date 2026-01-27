<?php

namespace App\Http\Controllers\Equipo;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmpresaController
{
    public function create()
    {
        $workers = User::whereIn('rol', [
                'sistemas',
                'gerente_general',
                'supervisor_contable',
                'contador_junior',
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'rol']);

        return view('equipo.empresas.create', compact('workers'));
    }
public function lookupRuc(string $ruc)
{
    $ruc = preg_replace('/\D+/', '', (string) $ruc);

    if (strlen($ruc) !== 11) {
        return response()->json([
            'ok' => false,
            'message' => 'El RUC debe tener 11 dígitos.',
        ], 422);
    }

    $caPath = 'C:\\laragon\\bin\\php\\php-8.3.16-Win32-vs16-x64\\extras\\ssl\\cacert-2025-12-02.pem';

    $token = config('services.aqpf.token'); // viene del .env

    if (!$token) {
        return response()->json([
            'ok' => false,
            'message' => 'Falta configurar AQPF_API_TOKEN en el .env',
        ], 500);
    }

    try {
        $res = Http::withOptions([
                'verify' => $caPath,
                'allow_redirects' => true,
            ])
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'PortalMendieta/1.0',
                'Authorization' => 'Bearer ' . $token, // ✅ AQUÍ estaba el problema
            ])
            ->timeout(15)
            ->get("https://apis.aqpfact.pe/api/ruc/{$ruc}");
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'message' => 'Error consultando proveedor (excepción).',
            'error' => $e->getMessage(),
        ], 502);
    }

 if (!$res->ok()) {
    $payload = [
        'ok' => false,
        'message' => 'No se pudo consultar el RUC (error de proveedor).',
    ];

    if (config('app.debug')) {
        $payload['status'] = $res->status();
        $payload['content_type'] = $res->header('content-type');
        $payload['body_preview'] = substr($res->body(), 0, 400);
    }

    return response()->json($payload, 502);
}


    $json = $res->json();

    if (!is_array($json)) {
        return response()->json([
            'ok' => false,
            'message' => 'Respuesta no es JSON',
            'status' => $res->status(),
            'content_type' => $res->header('content-type'),
            'body_preview' => substr($res->body(), 0, 400),
        ], 502);
    }

    if (!data_get($json, 'success')) {
        return response()->json([
            'ok' => false,
            'message' => 'RUC no encontrado o respuesta inválida.',
            'status' => $res->status(),
            'json' => $json,
        ], 404);
    }

    $d = data_get($json, 'data', []);

    return response()->json([
        'ok' => true,
        'data' => [
            'ruc' => data_get($d, 'ruc'),
            'razon_social' => data_get($d, 'nombre_o_razon_social'),
            'direccion_fiscal' => data_get($d, 'direccion_completa'),
            'departamento' => data_get($d, 'departamento'),
            'provincia' => data_get($d, 'provincia'),
            'distrito' => data_get($d, 'distrito'),
            'sunat_estado' => data_get($d, 'estado'),
            'sunat_condicion' => data_get($d, 'condicion'),
            'ubigeo' => data_get($d, 'ubigeo_sunat'),
        ],
        'raw' => $d,
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'ruc' => ['required', 'digits:11', 'unique:companies,ruc'],
        'razon_social' => ['required', 'string', 'max:255'],

        'correo_principal' => ['nullable', 'email', 'max:255'],
        'telefono' => ['nullable', 'string', 'max:50'],

        'departamento' => ['nullable', 'string', 'max:100'],
        'provincia' => ['nullable', 'string', 'max:100'],
        'distrito' => ['nullable', 'string', 'max:100'],
        'direccion_fiscal' => ['nullable', 'string', 'max:255'],
        'ubigeo' => ['nullable', 'string', 'max:10'],

        'sunat_estado' => ['nullable', 'string', 'max:50'],
        'sunat_condicion' => ['nullable', 'string', 'max:50'],

        'estado_interno' => ['required', 'in:Activo,Pendiente,Inactivo'],
        'notas_internas' => ['nullable', 'string'],

        'assigned_user_id' => ['nullable', 'exists:users,id'],

        'sunat_raw' => ['nullable', 'string'],

        // ✅ Cliente (2 en 1)
        'cliente_name' => ['required','string','max:120'],
        'cliente_email' => ['required','email','max:190','unique:users,email'],
        'cliente_password' => ['required','string','min:8','max:100'],
    ]);

    // Parsear sunat_raw string -> array para json cast
    $sunatRaw = null;
    if (!empty($validated['sunat_raw'])) {
        $decoded = json_decode($validated['sunat_raw'], true);
        if (is_array($decoded)) {
            $sunatRaw = $decoded;
        }
    }

    DB::transaction(function () use ($validated, $sunatRaw) {

        // 1) Crear empresa
        $company = Company::create([
            'ruc' => $validated['ruc'],
            'razon_social' => $validated['razon_social'],
            'correo_principal' => $validated['correo_principal'] ?? null,
            'telefono' => $validated['telefono'] ?? null,

            'departamento' => $validated['departamento'] ?? null,
            'provincia' => $validated['provincia'] ?? null,
            'distrito' => $validated['distrito'] ?? null,
            'direccion_fiscal' => $validated['direccion_fiscal'] ?? null,
            'ubigeo' => $validated['ubigeo'] ?? null,

            'sunat_estado' => $validated['sunat_estado'] ?? null,
            'sunat_condicion' => $validated['sunat_condicion'] ?? null,

            'estado_interno' => $validated['estado_interno'],
            'notas_internas' => $validated['notas_internas'] ?? null,

            'assigned_user_id' => $validated['assigned_user_id'] ?? null,

            'sunat_raw' => $sunatRaw,
        ]);

        // 2) Crear usuario cliente
        User::create([
            'name' => $validated['cliente_name'],
            'email' => $validated['cliente_email'],
            'rol' => 'cliente',
            'company_id' => $company->id,
            'password' => Hash::make($validated['cliente_password']),
            'email_verified_at' => now(), // temporal
        ]);
    });

    return redirect()
        ->route('equipo.empresas.create')
        ->with('ok', 'Empresa registrada con acceso al portal.');
}

 

    public function index()
{
    $companies = Company::query()
        ->with(['assignedUser:id,name,rol'])
        ->orderByDesc('id')
        ->paginate(12);

    return view('equipo.empresas.index', compact('companies'));
}



public function edit(Company $company)
{
    $workers = User::whereIn('rol', ['sistemas','gerente_general','supervisor_contable','contador_junior'])
        ->orderBy('name')
        ->get(['id','name','rol']);

    return view('equipo.empresas.edit', compact('company','workers'));
}

public function update(Request $request, Company $company)
{
    $validated = $request->validate([
        'ruc' => ['required','digits:11','unique:companies,ruc,'.$company->id],
        'razon_social' => ['required','string','max:255'],

        'correo_principal' => ['nullable','email','max:255'],
        'telefono' => ['nullable','string','max:50'],

        'departamento' => ['nullable','string','max:100'],
        'provincia' => ['nullable','string','max:100'],
        'distrito' => ['nullable','string','max:100'],
        'direccion_fiscal' => ['nullable','string','max:255'],
        'ubigeo' => ['nullable','string','max:10'],

        'sunat_estado' => ['nullable','string','max:50'],
        'sunat_condicion' => ['nullable','string','max:50'],

        'estado_interno' => ['required','in:Activo,Pendiente,Inactivo'],
        'notas_internas' => ['nullable','string'],
        'assigned_user_id' => ['nullable','exists:users,id'],
    ]);

    $company->update($validated);

    return redirect()
        ->route('equipo.empresas.index')
        ->with('ok', 'Empresa actualizada correctamente.');
}

public function destroy(Company $company)
{
    $company->delete();

    return redirect()
        ->route('equipo.empresas.index')
        ->with('ok', 'Empresa eliminada correctamente.');
}

public function togglePortal(Company $company)
{
    $company->portal_reportes_enabled = ! (bool) $company->portal_reportes_enabled;
    $company->save();

    return back()->with(
        'ok',
        $company->portal_reportes_enabled
            ? 'Portal de reportes habilitado.'
            : 'Portal de reportes deshabilitado.'
    );
}

public function show(Company $company)
{
    $company->load(['assignedUser:id,name,rol']);

    // Clientes asignados a esta empresa
    $clientesAsignados = User::query()
        ->where('rol', 'cliente')
        ->where('company_id', $company->id)
        ->orderBy('name')
        ->get(['id','name','email','company_id','email_verified_at','created_at']);

    // Clientes disponibles (no están en esta empresa)
    $clientesDisponibles = User::query()
        ->where('rol', 'cliente')
        ->where(function ($q) use ($company) {
            $q->whereNull('company_id')
              ->orWhere('company_id', '!=', $company->id);
        })
        ->orderBy('name')
        ->get(['id','name','email','company_id','email_verified_at','created_at']);

    return view('equipo.empresas.show', compact(
        'company',
        'clientesAsignados',
        'clientesDisponibles'
    ));
}

public function assignCliente(Request $request, Company $company, User $user)
{
    abort_unless($user->rol === 'cliente', 403);

    $user->company_id = $company->id;
    $user->save();

    return back()->with('ok', 'Cliente asignado a la empresa.');
}

public function unassignCliente(Request $request, Company $company, User $user)
{
    abort_unless($user->rol === 'cliente', 403);

    // Solo si realmente pertenece a esta empresa
    if ((int)$user->company_id === (int)$company->id) {
        $user->company_id = null;
        $user->save();
    }

    return back()->with('ok', 'Cliente removido de la empresa.');
}



}
