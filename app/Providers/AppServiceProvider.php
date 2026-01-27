<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

use App\Listeners\LogLogin;
use App\Listeners\LogLogout;
use App\Listeners\LogFailedLogin;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use App\Models\User;
use Illuminate\Support\Facades\Event;



use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       \Event::listen(Login::class, LogLogin::class);
        \Event::listen(Logout::class, LogLogout::class);
        \Event::listen(Failed::class, LogFailedLogin::class);

        // LOGIN: 5 intentos por minuto por (email + ip)
RateLimiter::for('login', function (Request $request) {
    $ip = $request->ip();
    $email = Str::lower((string) $request->input('email', ''));

    $key = $email !== '' ? "login:{$email}|{$ip}" : "login:ip:{$ip}";

    return Limit::perMinute(5)->by($key);
});

// FORGOT PASSWORD: 3 intentos cada 10 minutos
RateLimiter::for('password-reset', function (Request $request) {
    $ip = $request->ip();
    $email = Str::lower((string) $request->input('email', ''));

    return Limit::perMinutes(10, 3)->by("pwdreset:{$email}|{$ip}");
});

// RESEND VERIFICATION: 3 intentos cada 10 minutos
RateLimiter::for('verify-resend', function (Request $request) {
    $ip = $request->ip();
    $uid = optional($request->user())->id ?? 'guest';

    return Limit::perMinutes(10, 3)->by("verify:{$uid}|{$ip}");
});

User::created(function (User $user) {
        $user->profile()->create(); // crea fila vacía
    });

    }

    
}
