<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PortalReport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rows = PortalReport::query()
            ->with(['company:id,ruc,razon_social'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('titulo', 'like', "%{$q}%")
                        ->orWhereHas('company', function ($c) use ($q) {
                            $c->where('ruc', 'like', "%{$q}%")
                              ->orWhere('razon_social', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipo.reportes.index', compact('rows', 'q'));
    }

   public function create()
{
    $companies = Company::query()
    ->select(['id','ruc','razon_social'])
    ->whereHas('users', fn($u) => $u->where('rol','cliente'))
    ->orderBy('razon_social')
    ->get();


    return view('equipo.reportes.create', compact('companies'));
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required','integer','exists:companies,id'],
            'titulo' => ['required','string','max:190'],
            'periodo_mes' => ['nullable','integer','min:1','max:12'],
            'periodo_anio' => ['nullable','integer','min:2000','max:2100'],
            'estado' => ['required','in:borrador,publicado'],
            'powerbi_url_actual' => ['required','string','max:5000'],
            'nota_interna' => ['nullable','string','max:5000'],
        ]);

        $row = new PortalReport($data);
        $row->created_by = $request->user()->id;
        $row->updated_by = $request->user()->id;
        $row->save();

        return redirect()
            ->route('equipo.reportes.index')
            ->with('ok', 'Reporte creado.');
    }

    public function edit(PortalReport $reporte)
    {
        $reporte->load(['company:id,ruc,razon_social']);

       $companies = Company::query()
    ->select(['id','ruc','razon_social'])
    ->whereHas('users', fn($u) => $u->where('rol','cliente'))
    ->orderBy('razon_social')
    ->get();


        return view('equipo.reportes.edit', compact('reporte', 'companies'));
    }

    public function update(Request $request, PortalReport $reporte)
    {
        $data = $request->validate([
            'company_id' => ['required','integer','exists:companies,id'],
            'titulo' => ['required','string','max:190'],
            'periodo_mes' => ['nullable','integer','min:1','max:12'],
            'periodo_anio' => ['nullable','integer','min:2000','max:2100'],
            'estado' => ['required','in:borrador,publicado'],
            'powerbi_url_actual' => ['required','string','max:5000'],
            'nota_interna' => ['nullable','string','max:5000'],
        ]);

        $reporte->fill($data);
        $reporte->updated_by = $request->user()->id;
        $reporte->save();

        return redirect()
            ->route('equipo.reportes.index')
            ->with('ok', 'Reporte actualizado.');
    }

    public function destroy(PortalReport $reporte)
    {
        $reporte->delete();

        return redirect()
            ->route('equipo.reportes.index')
            ->with('ok', 'Reporte eliminado.');
    }

public function show(\App\Models\PortalReport $reporte)
{
    $reporte->load(['company:id,ruc,razon_social']);

    $company = $reporte->company; // ✅ ahora existe en la vista

    return view('equipo.reportes.show', compact('reporte', 'company'));
}




}
