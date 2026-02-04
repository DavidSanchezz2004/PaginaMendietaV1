<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use App\Models\PortalCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CredentialController extends Controller
{
    private array $allowedPortals = ['SUNAT', 'SUNAFIL','AFPNET'];

    // GET /equipo/credenciales
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $accounts = PortalAccount::query()
            ->with(['company:id,ruc,razon_social', 'latestCredential'])
            ->whereIn('portal', $this->allowedPortals)
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('company', function ($c) use ($q) {
                    $c->where('ruc', 'like', "%{$q}%")
                      ->orWhere('razon_social', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipo.credenciales.index', compact('accounts', 'q'));
    }

    // GET /equipo/credenciales/{portalAccount}/create
    public function create(PortalAccount $portalAccount)
    {
        $portalAccount->load(['company:id,ruc,razon_social', 'latestCredential']);

        // seguridad: solo sunat/sunafil/afpnet
        if (!in_array(strtoupper($portalAccount->portal), $this->allowedPortals, true)) {
            abort(404);
        }

        return view('equipo.credenciales.create', compact('portalAccount'));
    }

    // POST /equipo/credenciales/{portalAccount}
    public function store(Request $request, PortalAccount $portalAccount)
    {
        if (!in_array(strtoupper($portalAccount->portal), $this->allowedPortals, true)) {
            abort(404);
        }

        $validated = $request->validate([
            'username' => ['required','string','max:255'],
            'password' => ['required','string','max:255'],
        ]);

        $username = trim((string) $validated['username']);
        $password = (string) $validated['password'];

        // ✅ FIX: updateOrCreate en lugar de create (evita duplicate entry)
        PortalCredential::updateOrCreate(
            ['portal_account_id' => $portalAccount->id],
            [
                'username_enc' => Crypt::encryptString($username),
                'password_enc' => Crypt::encryptString($password),
            ]
        );

        return redirect()
            ->route('equipo.credenciales.index')
            ->with('ok', 'Credenciales guardadas/rotadas correctamente.');
    }

    public function edit(PortalAccount $portalAccount)
    {
        return $this->create($portalAccount);
    }

    public function update(Request $request, PortalAccount $portalAccount)
    {
        return $this->store($request, $portalAccount);
    }
}