<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\PortalAccount;
use App\Models\PortalAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    // GET /equipo/asignaciones
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $assignments = PortalAssignment::query()
            ->with([
                'appUser:id,username,status',
                'portalAccount.company:id,ruc,razon_social',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('appUser', fn($u) =>
                    $u->where('username', 'like', "%{$q}%")
                )->orWhereHas('portalAccount.company', fn($c) =>
                    $c->where('ruc', 'like', "%{$q}%")
                      ->orWhere('razon_social', 'like', "%{$q}%")
                );
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipo.asignaciones.index', compact('assignments', 'q'));
    }

    // GET /equipo/asignaciones/create
    public function create()
    {
        $operators = AppUser::query()
            ->orderBy('username')
            ->get(['id','username','status']);

        $accounts = PortalAccount::query()
            ->with('company:id,ruc,razon_social')
            ->orderByDesc('id')
            ->get(['id','company_id','portal','status']);

        return view('equipo.asignaciones.create', compact('operators','accounts'));
    }

    // POST /equipo/asignaciones
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_user_id' => ['required','integer','exists:app_users,id'],
            'portal_account_id' => ['required','integer','exists:portal_accounts,id'],
            'active' => ['nullable','boolean'],
        ]);

        // Validación extra: portal_account debe estar activo
        $account = PortalAccount::query()->findOrFail($validated['portal_account_id']);
        if ($account->status !== 'active') {
            return back()->withErrors(['portal_account_id' => 'El portal_account no está activo.'])->withInput();
        }

        // upsert para no chocar con unique(portal_account_id, app_user_id)
        $assignment = PortalAssignment::updateOrCreate(
            [
                'portal_account_id' => $validated['portal_account_id'],
                'app_user_id' => $validated['app_user_id'],
            ],
            [
                'active' => (bool)($validated['active'] ?? true),
                'assigned_by' => Auth::id(),
            ]
        );

        return redirect()
            ->route('equipo.asignaciones.index')
            ->with('ok', 'Asignación guardada correctamente.');
    }

    // PATCH /equipo/asignaciones/{assignment}/toggle
    public function toggle(PortalAssignment $assignment)
    {
        $assignment->active = ! $assignment->active;
        $assignment->assigned_by = Auth::id();
        $assignment->save();

        return back()->with('ok', 'Estado de asignación actualizado.');
    }

    // DELETE /equipo/asignaciones/{assignment}
    public function destroy(PortalAssignment $assignment)
    {
        $assignment->delete();

        return redirect()
            ->route('equipo.asignaciones.index')
            ->with('ok', 'Asignación eliminada.');
    }
}
