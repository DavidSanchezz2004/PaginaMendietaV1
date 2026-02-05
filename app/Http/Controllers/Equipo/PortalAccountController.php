<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PortalAccount;
use Illuminate\Http\Request;

class PortalAccountController extends Controller
{
    // ✅ Debe calzar con tu ENUM: enum('sunat','sunafil','afpnet')
    private array $portals = ['sunat','sunafil','afpnet'];

    // ✅ Mapea aliases viejos -> nuevos
    private array $portalAlias = [
        'afp' => 'afpnet',
    ];

    private function normalizePortal(string $portal): string
    {
        $portal = strtolower(trim($portal));
        return $this->portalAlias[$portal] ?? $portal;
    }

    public function edit(Company $company)
    {
        $existing = PortalAccount::query()
            ->where('company_id', $company->id)
            ->get()
            ->keyBy('portal');

        $states = [];
        foreach ($this->portals as $p) {
            $states[$p] = ($existing[$p]->status ?? null) === 'active';
        }

        return view('equipo.empresas.portales', compact('company', 'states'));
    }

    public function update(Request $request, Company $company)
    {
        $enabled = $request->input('portals', []);
        $enabled = is_array($enabled) ? $enabled : [];

        // ✅ normaliza aliases (afp -> afpnet)
        $enabled = array_map(fn($p) => $this->normalizePortal((string)$p), $enabled);

        // ✅ deja solo permitidos
        $enabled = array_values(array_intersect($enabled, $this->portals));

        foreach ($this->portals as $portal) {
            $pa = PortalAccount::firstOrCreate(
                ['company_id' => $company->id, 'portal' => $portal],
                ['status' => 'inactive', 'created_by' => optional($request->user())->id]
            );

            $pa->status = in_array($portal, $enabled, true) ? 'active' : 'inactive';
            if (! $pa->created_by) $pa->created_by = optional($request->user())->id;
            $pa->save();
        }

        return redirect()
            ->route('equipo.empresas.show', $company)
            ->with('ok', 'Portales actualizados correctamente.');
    }
}
