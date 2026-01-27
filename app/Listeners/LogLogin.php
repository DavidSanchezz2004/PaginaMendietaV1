<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;




class LogLogin
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
    public function handle(Login $event): void
    {
        AuditLog::create([
            'user_id' => $event->user->id,
            'event' => 'login',
            'route' => Request::path(),
            'ip' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 500),
            'meta' => [
                'guard' => $event->guard,
            ],
        ]);
    }
}
