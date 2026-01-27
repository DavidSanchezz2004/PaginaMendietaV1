<?php

/*
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalJobResult extends Model
{
    protected $fillable = [
        'portal_job_id','ok','data','error','evidence',
    ];

    protected $casts = [
        'ok' => 'boolean',
        'data' => 'array',
        'evidence' => 'array',
    ];

    public function job() { return $this->belongsTo(PortalJob::class, 'portal_job_id'); }
}
*/


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalJobResult extends Model
{
    protected $table = 'portal_job_results';

    protected $fillable = [
        'portal_job_id',
        'ok',
        'data',
        'evidences', // ✅ coincide con la BD
    ];

    protected $casts = [
        'ok' => 'boolean',
        'data' => 'array',
        'evidences' => 'array', // ✅ coincide con la BD
    ];

    public function job()
    {
        return $this->belongsTo(PortalJob::class, 'portal_job_id');
    }
}
