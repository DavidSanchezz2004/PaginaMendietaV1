<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\AppDevice;
use App\Models\AppUser;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required','string','max:255'],
            'password' => ['required','string','max:255'],
            'device_id' => ['required','string','max:120'],
            'device_name' => ['nullable','string','max:120'],
        ]);

        $user = AppUser::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            AuditLog::create([
                'user_id' => null,
                'event' => 'app_login_fail',
                'route' => 'api/app/login',
                'ip' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 500),
                'meta' => ['username' => $data['username']],
            ]);
            return response()->json(['ok' => false, 'message' => 'Credenciales inválidas.'], 401);
        }

        if ($user->status !== 'activo') {
            return response()->json(['ok' => false, 'message' => 'Usuario inactivo.'], 403);
        }

        $device = AppDevice::firstOrCreate(
            ['app_user_id' => $user->id, 'device_id' => $data['device_id']],
            ['device_name' => $data['device_name'] ?? null, 'status' => 'active', 'first_seen_at' => now()]
        );

        if ($device->status !== 'active') {
            return response()->json(['ok' => false, 'message' => 'Device bloqueado.'], 403);
        }

        $device->device_name = $data['device_name'] ?? $device->device_name;
        $device->last_seen_at = now();
        $device->save();

        $user->last_login_at = now();
        $user->save();

        // token Sanctum largo
        $token = $user->createToken('tauri-app', ['app'])->plainTextToken;

        AuditLog::create([
            'user_id' => null,
            'event' => 'app_login_ok',
            'route' => 'api/app/login',
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
            'meta' => ['app_user_id' => $user->id, 'device_id' => $data['device_id']],
        ]);

        return response()->json([
            'ok' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
            ],
            'device' => [
                'device_id' => $device->device_id,
                'status' => $device->status,
            ],
        ]);
    }

    public function me(Request $request)
    {
        /** @var \App\Models\AppUser $u */
        $u = $request->user();
        return response()->json([
            'ok' => true,
            'user' => ['id' => $u->id, 'username' => $u->username, 'status' => $u->status],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['ok' => true]);
    }
}
