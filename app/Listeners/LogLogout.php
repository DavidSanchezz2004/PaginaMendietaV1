<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogLogout
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
    public function handle(Logout $event): void
    {
        AuditLog::create([
            'user_id' => optional($event->user)->id,
            'event' => 'logout',
            'route' => Request::path(),
            'ip' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 500),
            'meta' => [
                'guard' => $event->guard,
            ],
        ]);
    }
}
