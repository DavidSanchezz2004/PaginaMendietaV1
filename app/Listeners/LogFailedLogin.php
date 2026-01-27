<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use App\Models\AuditLog;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogFailedLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
         // Ojo: NO guardamos password ni datos sensibles
        $email = Str::lower((string) Request::input('email', ''));

        AuditLog::create([
            'user_id' => optional($event->user)->id, // normalmente null cuando falla
            'event' => 'login_failed',
            'route' => Request::path(),
            'ip' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 500),
            'meta' => [
                'email' => $email,
                'guard' => $event->guard,
            ],
        ]);
    }
}
