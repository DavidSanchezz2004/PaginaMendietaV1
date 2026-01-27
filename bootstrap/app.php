<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ✅ AÑADE ESTO
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxies (para HTTPS detrás de proxy/Cloudflare/EasyPanel)
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL);
        
        $middleware->alias([
            // ✅ Aliases de seguridad / acceso
            'role'         => \App\Http\Middleware\RoleMiddleware::class,
            'app_user'     => \App\Http\Middleware\EnsureAppUser::class,
            'device_bound' => \App\Http\Middleware\EnsureDeviceBound::class,
            'recent_mfa'   => \App\Http\Middleware\RequireRecentMfa::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {

            // Detecta el "uri" real de la ruta (ej: login, forgot-password, email/verification-notification)
            $uri = $request->route()?->uri() ?? $request->path();

            // Solo para estos endpoints
            $map = [
                'login' => [
                    'event' => 'login_throttled',
                    'method' => 'POST',
                    'throttle' => 'throttle:login',
                ],
                'forgot-password' => [
                    'event' => 'password_reset_throttled',
                    'method' => 'POST',
                    'throttle' => 'throttle:password-reset',
                ],
                'email/verification-notification' => [
                    'event' => 'verify_resend_throttled',
                    'method' => 'POST',
                    'throttle' => 'throttle:verify-resend',
                ],
            ];

            if (! isset($map[$uri])) {
                return null; // no nos interesa
            }

            // Validamos método
            if (strtoupper($request->method()) !== $map[$uri]['method']) {
                return null;
            }

            // Validamos que el 429 viene de NUESTRO throttle (no de otro lado)
            $middlewares = $request->route()?->gatherMiddleware() ?? [];
            $expectedThrottle = $map[$uri]['throttle'];

            $isExpected = collect($middlewares)->contains(fn ($m) => str_contains($m, $expectedThrottle));

            if (! $isExpected) {
                return null;
            }

            $email = Str::lower((string) $request->input('email', ''));

            AuditLog::create([
                'user_id' => optional($request->user())->id,
                'event' => $map[$uri]['event'],
                'route' => $uri,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'meta' => [
                    'email' => $email,
                    'status' => 429,
                ],
            ]);

            // dejamos que Laravel siga devolviendo el 429 normal
            return null;
        });

    })
    ->create();
