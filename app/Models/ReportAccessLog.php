<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportAccessLog extends Model
{
    protected $table = 'report_access_logs';

    protected $fillable = [
        'user_id',
        'company_id',
        'reporte_id',
        'ok',
        'reason',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected $casts = [
        'ok' => 'boolean',
        'accessed_at' => 'datetime',
    ];
}
