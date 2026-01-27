<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\UserMfa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OTPHP\TOTP;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class MfaController extends Controller
{
    public function setup(Request $request)
    {
        $user = $request->user();

        $mfa = $user->mfa ?: UserMfa::create(['user_id' => $user->id]);

        if (!$mfa->totp_secret) {
            $totp = TOTP::create(); // genera secret
            $mfa->totp_secret = $totp->getSecret();
            $mfa->save();
        }

        // provisioning uri
        $totp = TOTP::create($mfa->totp_secret);
        $totp->setLabel($user->email);
        $totp->setIssuer(config('app.name'));
        $uri = $totp->getProvisioningUri();

        // QR SVG
        $renderer = new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($uri);

        // Recovery codes (solo si no existen)
        $plainCodes = null;
        if (empty($mfa->recovery_codes)) {
            $plainCodes = collect(range(1, 10))->map(fn() => Str::upper(Str::random(10)))->all();
            $hashed = array_map(fn($c) => Hash::make($c), $plainCodes);
            $mfa->recovery_codes = $hashed;
            $mfa->save();
            // Guardamos los “plain” en sesión para mostrarlos una vez
            session(['mfa_recovery_plain' => $plainCodes]);
        } else {
            $plainCodes = session('mfa_recovery_plain'); // si aún existen en sesión
        }

        return view('equipo.mfa.setup', [
            'qrSvg' => $qrSvg,
            'recoveryCodes' => $plainCodes,
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => ['required','string'],
        ]);

        $user = $request->user();
        $mfa = $user->mfa;

        if (!$mfa || !$mfa->totp_secret) {
            return redirect()->route('equipo.mfa.setup');
        }

        $totp = TOTP::create($mfa->totp_secret);
        $totp->setIssuer(config('app.name'));
        $totp->setLabel($user->email);

        $ok = $totp->verify($request->string('code'), null, 1); // ventana ±1

        if (!$ok) {
            return back()->withErrors(['code' => 'Código inválido.'])->withInput();
        }

        $mfa->enabled = true;
        $mfa->confirmed_at = now();
        $mfa->last_verified_at = now();
        $mfa->save();

        session(['mfa_verified' => true]);
        session()->forget('mfa_recovery_plain');

        AuditLog::create([
            'user_id' => $user->id,
            'event' => 'mfa_enabled',
            'route' => 'equipo/mfa',
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
            'meta' => ['status' => 'enabled'],
        ]);

        return redirect()->route('equipo.dashboard');
    }

    public function disable(Request $request)
    {
        $user = $request->user();
        $mfa = $user->mfa;

        if ($mfa) {
            $mfa->enabled = false;
            $mfa->totp_secret = null;
            $mfa->recovery_codes = null;
            $mfa->confirmed_at = null;
            $mfa->save();
        }

        session()->forget('mfa_verified');

        AuditLog::create([
            'user_id' => $user->id,
            'event' => 'mfa_disabled',
            'route' => 'equipo/mfa',
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
            'meta' => ['status' => 'disabled'],
        ]);

        return redirect()->route('equipo.mfa.setup');
    }
}
