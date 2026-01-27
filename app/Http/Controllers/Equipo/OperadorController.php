<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OperadorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $items = AppUser::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('username', 'like', "%{$q}%")
                   ->orWhere('status', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('equipo.operadores.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('equipo.operadores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:app_users,username'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'status'   => ['required', Rule::in(['activo', 'inactivo'])],

            'type' => ['required', Rule::in(['equipo', 'cliente'])],

            // si es cliente: plan requerido
            'plan' => [
                'nullable',
                Rule::in(['starter', 'oro', 'pro', 'empresa']),
                function ($attr, $value, $fail) use ($request) {
                    if ($request->input('type') === 'cliente' && empty($value)) {
                        $fail('El plan es obligatorio cuando el tipo es cliente.');
                    }
                }
            ],

            // override manual (solo aplica a cliente)
            'max_companies' => ['nullable', 'integer', 'min:1', 'max:200'],

            'subscription_status' => ['required', Rule::in(['active', 'overdue', 'suspended'])],
        ]);

        $type = $validated['type'];

        AppUser::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'status'   => $validated['status'],

            'type' => $type,
            'plan' => $type === 'cliente'
                ? ($validated['plan'] ?? 'starter')
                : 'starter',

            'max_companies' => $type === 'cliente'
                ? ($validated['max_companies'] ?? null)
                : null,

            'subscription_status' => $validated['subscription_status'],
        ]);

        return redirect()->route('equipo.operadores.index')->with('ok', 'Operador creado.');
    }

    public function edit(AppUser $operador)
    {
        return view('equipo.operadores.edit', compact('operador'));
    }

    public function update(Request $request, AppUser $operador)
    {
        $validated = $request->validate([
            'username' => [
                'required', 'string', 'max:60', 'alpha_dash',
                Rule::unique('app_users', 'username')->ignore($operador->id)
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
            'status'   => ['required', Rule::in(['activo', 'inactivo'])],

            'type' => ['required', Rule::in(['equipo', 'cliente'])],

            'plan' => [
                'nullable',
                Rule::in(['starter', 'oro', 'pro', 'empresa']),
                function ($attr, $value, $fail) use ($request) {
                    if ($request->input('type') === 'cliente' && empty($value)) {
                        $fail('El plan es obligatorio cuando el tipo es cliente.');
                    }
                }
            ],

            'max_companies' => ['nullable', 'integer', 'min:1', 'max:200'],

            'subscription_status' => ['required', Rule::in(['active', 'overdue', 'suspended'])],
        ]);

        $type = $validated['type'];

        $operador->username = $validated['username'];
        $operador->status   = $validated['status'];

        $operador->type = $type;
        $operador->plan = $type === 'cliente'
            ? ($validated['plan'] ?? 'starter')
            : 'starter';

        $operador->max_companies = $type === 'cliente'
            ? ($validated['max_companies'] ?? null)
            : null;

        $operador->subscription_status = $validated['subscription_status'];

        if (!empty($validated['password'])) {
            $operador->password = Hash::make($validated['password']);
        }

        $operador->save();

        return redirect()->route('equipo.operadores.index')->with('ok', 'Operador actualizado.');
    }

    public function destroy(AppUser $operador)
    {
        $operador->delete();

        return redirect()->route('equipo.operadores.index')->with('ok', 'Operador eliminado.');
    }
}
