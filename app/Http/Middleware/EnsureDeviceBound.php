<?php

namespace App\Http\Middleware;

use App\Models\AppDevice;
use Closure;
use Illuminate\Http\Request;

class EnsureDeviceBound
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\AppUser $u */
        $u = $request->user();

        $deviceId = (string) $request->header('X-Device-Id', '');
        $deviceId = preg_replace('/\s+/', '', $deviceId);

        if (strlen($deviceId) < 6) {
            return response()->json(['ok' => false, 'message' => 'Falta header X-Device-Id.'], 422);
        }

        $device = AppDevice::where('app_user_id', $u->id)
            ->where('device_id', $deviceId)
            ->first();

        if (! $device) {
            return response()->json(['ok' => false, 'message' => 'Device no registrado.'], 403);
        }

        if ($device->status !== 'active') {
            return response()->json(['ok' => false, 'message' => 'Device bloqueado.'], 403);
        }

        $device->last_seen_at = now();
        $device->save();

        // guardamos para uso en controllers
        $request->attributes->set('device_id', $deviceId);

        return $next($request);
    }
}
