<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OTPHP\TOTP;
use Illuminate\Support\Facades\Auth;

class MfaChallengeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->rol === 'cliente') {
            return redirect()->route('home');
        }

        $mfa = $user->mfa;
        if (!$mfa || !$mfa->enabled) {
            return redirect()->route('equipo.mfa.setup');
        }

        return view('auth.mfa-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required','string'],
        ]);

        $user = $request->user();
        $mfa = $user->mfa;

        $code = Str::upper(trim((string)$request->input('code')));

        // ✅ reutilizamos lógica
        $result = $this->checkMfaCode($user, $mfa, $code);

        if ($result['ok']) {
            session([
                'mfa_verified' => true,
                'mfa.recent_at' => now()->timestamp, // 👈 importante para acciones sensibles
            ]);

            $mfa->last_verified_at = now();
            $mfa->save();

            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'mfa_challenge_ok',
                'route' => 'mfa/challenge',
                'ip' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 500),
                'meta' => ['method' => $result['method']],
            ]);

            return redirect()->to(session()->pull('url.intended', route('equipo.dashboard')));
        }

        AuditLog::create([
            'user_id' => $user->id,
            'event' => 'mfa_challenge_fail',
            'route' => 'mfa/challenge',
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
            'meta' => ['status' => 'invalid_code'],
        ]);

        return back()->withErrors(['code' => 'Código inválido.'])->withInput();
    }

    public function confirmShow()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // si no tiene mfa habilitado, no hay nada que confirmar
        if (!optional($user->mfa)->enabled) {
            return redirect()->route('equipo.dashboard');
        }

        return view('equipo.mfa.confirm');
    }

    public function confirmVerify(Request $request)
    {
        $request->validate([
            'code' => ['required','string'],
        ]);

        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $mfa = $user->mfa;
        if (!$mfa || !$mfa->enabled) {
            return redirect()->route('equipo.dashboard');
        }

        $code = Str::upper(trim((string)$request->input('code')));

        $result = $this->checkMfaCode($user, $mfa, $code);

        if (!$result['ok']) {
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'mfa_challenge_fail',
                'route' => 'mfa/confirm',
                'ip' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 500),
                'meta' => ['status' => 'invalid_code'],
            ]);

            return back()->withErrors(['code' => 'Código inválido.'])->withInput();
        }

        // ✅ marca como "reciente" (10min lo valida el middleware)
        session(['mfa.recent_at' => now()->timestamp]);

        // (opcional) actualizar last_verified_at también
        $mfa->last_verified_at = now();
        $mfa->save();

        AuditLog::create([
            'user_id' => $user->id,
            'event' => 'mfa_challenge_ok',
            'route' => 'mfa/confirm',
            'ip' => $request->ip(),
            'user_agent' => substr((string)$request->userAgent(), 0, 500),
            'meta' => ['method' => $result['method']],
        ]);

        $to = session('mfa.intended', route('equipo.dashboard'));
        session()->forget('mfa.intended');

        return redirect()->to($to);
    }

    /**
     * Verifica un código MFA: TOTP o Recovery.
     * Retorna ['ok'=>bool, 'method'=>'totp'|'recovery'|null]
     */
    private function checkMfaCode($user, $mfa, string $code): array
    {
        // 1) TOTP
        $totp = TOTP::create($mfa->totp_secret);
        $totp->setIssuer(config('app.name'));
        $totp->setLabel($user->email);

        if ($totp->verify($code, null, 1)) {
            return ['ok' => true, 'method' => 'totp'];
        }

        // 2) Recovery code
        $codes = $mfa->recovery_codes ?? [];
        foreach ($codes as $i => $hashed) {
            if (Hash::check($code, $hashed)) {
                unset($codes[$i]);
                $mfa->recovery_codes = array_values($codes);
                $mfa->save();

                return ['ok' => true, 'method' => 'recovery'];
            }
        }

        return ['ok' => false, 'method' => null];
    }
}
