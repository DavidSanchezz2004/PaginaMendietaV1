<?php
// App\Models\PortalReportAccessLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalReportAccessLog extends Model
{
    protected $fillable = [
        'user_id','company_id','reporte_id','ok','reason',
        'ip_address','user_agent',
    ];
}
