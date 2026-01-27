<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Company; // 👈 agrega esto arriba

class UsuarioController extends Controller
{
    private array $rolesPermitidos = [
        'sistemas',
        'gerente_general',
        'supervisor_contable',
        'contador_junior',
        'cliente',
    ];

    private function assertCanManageUsers(Request $request): void
    {
        $u = $request->user();
        $rol = $u->rol ?? null;

        if (!in_array($rol, ['sistemas', 'gerente_general'], true)) {
            abort(403, 'No autorizado para gestionar usuarios.');
        }
    }

    public function index(Request $request)
    {
        $this->assertCanManageUsers($request);

        $q = trim((string) $request->query('q', ''));

        $rows = User::query()
            ->with('profile')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('rol', 'like', "%{$q}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('equipo.usuarios.index', compact('rows', 'q'));
    }

    // public function create(Request $request)
    // {
    //     $this->assertCanManageUsers($request);

    //     $roles = $this->rolesPermitidos;

    //     return view('equipo.usuarios.create', compact('roles'));
    // }

 public function create(Request $request)
{
    $this->assertCanManageUsers($request);

    $roles = $this->rolesPermitidos;

    // ✅ para el modal
    $companies = Company::query()
        ->orderBy('razon_social')
        ->get(['id','razon_social','ruc']);

    return view('equipo.usuarios.create', compact('roles', 'companies'));
}

    // public function store(Request $request)
    // {
    //     $this->assertCanManageUsers($request);

    //     $data = $request->validate([
    //         'name' => ['required', 'string', 'max:120'],
    //         'email' => ['required', 'email', 'max:190', 'unique:users,email'],
    //         'rol' => ['required', 'string', Rule::in($this->rolesPermitidos)],
    //         'password' => ['required', 'string', 'min:8', 'max:100'],

    //         'country' => ['nullable', 'string', 'max:80'],
    //         'city' => ['nullable', 'string', 'max:80'],
    //         'postal_code' => ['nullable', 'string', 'max:20'],
    //         'document_type' => ['nullable', 'string', 'max:20'],
    //         'document_number' => ['nullable', 'string', 'max:30'],
    //         'phone' => ['nullable', 'string', 'max:30'],
    //         'bio' => ['nullable', 'string', 'max:255'],
    //     ]);

    //     $user = new User();
    //     $user->name = $data['name'];
    //     $user->email = $data['email'];
    //     $user->rol = $data['rol'];
    //     $user->password = Hash::make($data['password']);

    //     // ✅ Solo CLIENTE se marca verificado automáticamente.
    //     // Internos deben verificar por correo (mantiene seguridad y control).
    //     if ($data['rol'] === 'cliente') {
    //         $user->email_verified_at = now();
    //     } else {
    //         $user->email_verified_at = null; // explícito: requiere verificación
    //     }

    //     $user->save();

    //     UserProfile::updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'country' => $data['country'] ?? null,
    //             'city' => $data['city'] ?? null,
    //             'postal_code' => $data['postal_code'] ?? null,
    //             'document_type' => $data['document_type'] ?? null,
    //             'document_number' => $data['document_number'] ?? null,
    //             'phone' => $data['phone'] ?? null,
    //             'bio' => $data['bio'] ?? null,
    //         ]
    //     );

    //     // ✅ Para internos: mandar correo de verificación al crearlo desde admin
    //     if ($user->rol !== 'cliente' && !$user->hasVerifiedEmail()) {
    //         $user->sendEmailVerificationNotification();
    //     }

    //     return redirect()->route('equipo.usuarios.index')->with('status', 'Usuario creado.');
    // }

public function store(Request $request)
{
    $this->assertCanManageUsers($request); // ✅ seguridad

    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','email','max:255','unique:users,email'],
        'rol' => ['required', Rule::in($this->rolesPermitidos)],
        'password' => ['required','string','min:8'],

        // ✅ si rol=cliente => obligatorio; si no, nullable
        'company_id' => ['nullable','integer','exists:companies,id','required_if:rol,cliente'],
    ], [
        'company_id.required_if' => 'Selecciona una empresa para el cliente.',
    ]);

    // ✅ blindaje: si NO es cliente, company_id siempre null
    if (($data['rol'] ?? null) !== 'cliente') {
        $data['company_id'] = null;
    }

    $user = new User();
    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->rol = $data['rol'];
    $user->company_id = $data['company_id'];
    $user->password = bcrypt($data['password']);

    // ✅ clientes verificados para pasar middleware verified
    if ($data['rol'] === 'cliente') {
        $user->email_verified_at = now();
    } else {
        $user->email_verified_at = null;
    }

    $user->save();

    return redirect()->route('equipo.usuarios.index')->with('ok', 'Usuario creado.');
}


    public function edit(Request $request, User $user)
{
    $this->assertCanManageUsers($request);

    $roles = $this->rolesPermitidos;
    $profile = $user->profile;

    // ✅ para el modal
    $companies = Company::query()
        ->orderBy('razon_social')
        ->get(['id','razon_social','ruc']);

    return view('equipo.usuarios.edit', compact('user', 'roles', 'profile', 'companies'));
}

    // public function update(Request $request, User $user)
    // {
    //     $this->assertCanManageUsers($request);

    //     $data = $request->validate([
    //         'name' => ['required', 'string', 'max:120'],
    //         'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
    //         'rol' => ['required', 'string', Rule::in($this->rolesPermitidos)],
    //         'password' => ['nullable', 'string', 'min:8', 'max:100'],

    //         'country' => ['nullable', 'string', 'max:80'],
    //         'city' => ['nullable', 'string', 'max:80'],
    //         'postal_code' => ['nullable', 'string', 'max:20'],
    //         'document_type' => ['nullable', 'string', 'max:20'],
    //         'document_number' => ['nullable', 'string', 'max:30'],
    //         'phone' => ['nullable', 'string', 'max:30'],
    //         'bio' => ['nullable', 'string', 'max:255'],
    //     ]);

    //     $oldRol = $user->rol;
    //     $oldEmail = $user->email;

    //     $user->name = $data['name'];
    //     $user->email = $data['email'];
    //     $user->rol = $data['rol'];

    //     if (!empty($data['password'])) {
    //         $user->password = Hash::make($data['password']);
    //     }

    //     // Si cambió el email, hay que re-verificar (solo para internos)
    //     $emailCambio = ($oldEmail !== $user->email);

    //     if ($user->rol === 'cliente') {
    //         // Cliente siempre verificado
    //         $user->email_verified_at = now();
    //     } else {
    //         // Interno: si cambió email o venía sin verificación, forzar verificación
    //         if ($emailCambio) {
    //             $user->email_verified_at = null;
    //         }
    //     }

    //     $user->save();

    //     UserProfile::updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'country' => $data['country'] ?? null,
    //             'city' => $data['city'] ?? null,
    //             'postal_code' => $data['postal_code'] ?? null,
    //             'document_type' => $data['document_type'] ?? null,
    //             'document_number' => $data['document_number'] ?? null,
    //             'phone' => $data['phone'] ?? null,
    //             'bio' => $data['bio'] ?? null,
    //         ]
    //     );

    //     // Si pasó a interno o cambió email siendo interno → enviar verificación
    //     if ($user->rol !== 'cliente' && !$user->hasVerifiedEmail()) {
    //         $user->sendEmailVerificationNotification();
    //     }

    //     return redirect()->route('equipo.usuarios.index')->with('status', 'Usuario actualizado.');
    // }

    public function update(Request $request, User $user)
{
    $this->assertCanManageUsers($request);

    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
        'rol' => ['required', Rule::in($this->rolesPermitidos)],
        'password' => ['nullable', 'string', 'min:8', 'max:100'],

        // ✅ empresa (solo cliente)
        'company_id' => ['nullable','integer','exists:companies,id','required_if:rol,cliente'],

        'country' => ['nullable', 'string', 'max:80'],
        'city' => ['nullable', 'string', 'max:80'],
        'postal_code' => ['nullable', 'string', 'max:20'],
        'document_type' => ['nullable', 'string', 'max:20'],
        'document_number' => ['nullable', 'string', 'max:30'],
        'phone' => ['nullable', 'string', 'max:30'],
        'bio' => ['nullable', 'string', 'max:255'],
    ], [
        'company_id.required_if' => 'Selecciona una empresa para el cliente.',
    ]);

    $oldEmail = $user->email;

    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->rol = $data['rol'];

    // ✅ blindaje: si NO es cliente, company_id siempre null
    if (($data['rol'] ?? null) !== 'cliente') {
        $user->company_id = null;
    } else {
        $user->company_id = $data['company_id'];
    }

    if (!empty($data['password'])) {
        $user->password = Hash::make($data['password']);
    }

    // Si cambió el email, hay que re-verificar (solo para internos)
    $emailCambio = ($oldEmail !== $user->email);

    if ($user->rol === 'cliente') {
        // Cliente siempre verificado (porque usas middleware verified)
        $user->email_verified_at = now();
    } else {
        if ($emailCambio) {
            $user->email_verified_at = null;
        }
    }

    $user->save();

    UserProfile::updateOrCreate(
        ['user_id' => $user->id],
        [
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'document_type' => $data['document_type'] ?? null,
            'document_number' => $data['document_number'] ?? null,
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]
    );

    // Si interno sin verificación → enviar correo
    if ($user->rol !== 'cliente' && !$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
    }

    return redirect()->route('equipo.usuarios.index')->with('status', 'Usuario actualizado.');
}


    public function destroy(Request $request, User $user)
    {
        $this->assertCanManageUsers($request);

        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        UserProfile::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->route('equipo.usuarios.index')->with('status', 'Usuario eliminado.');
    }
}
